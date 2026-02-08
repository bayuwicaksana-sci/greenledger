<?php

namespace App\Services;

use App\Models\BudgetCommitment;
use App\Models\CoaAccount;
use App\Models\FiscalYear;
use App\Models\PaymentRequest;
use App\Models\Program;
use App\Notifications\FiscalYearClosedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FiscalYearService
{
    public function __construct(
        protected FiscalYearReportGenerator $reportGenerator,
    ) {}

    /**
     * Copy COA structure from source fiscal year to target fiscal year.
     *
     * @return int Number of accounts copied
     */
    public function copyCoaStructure(
        FiscalYear $sourceFY,
        FiscalYear $targetFY,
    ): int {
        return DB::transaction(function () use ($sourceFY, $targetFY) {
            $copiedCount = 0;
            $codeToNewId = [];

            // Get all accounts from source FY, sorted by hierarchy
            $sourceAccounts = CoaAccount::where('fiscal_year_id', $sourceFY->id)
                ->orderBy('hierarchy_level')
                ->orderBy('account_code')
                ->get();

            foreach ($sourceAccounts as $source) {
                // Check if account already exists in target FY
                $exists = CoaAccount::where('fiscal_year_id', $targetFY->id)
                    ->where('site_id', $source->site_id)
                    ->where('account_code', $source->account_code)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Resolve parent in new FY
                $newParentId = null;
                if ($source->parent_account_id) {
                    $parentCode = CoaAccount::find($source->parent_account_id)
                        ?->account_code;
                    if (
                        $parentCode &&
                        isset($codeToNewId[$source->site_id][$parentCode])
                    ) {
                        $newParentId =
                            $codeToNewId[$source->site_id][$parentCode];
                    }
                }

                // Create copy in target FY
                $copy = CoaAccount::create([
                    'site_id' => $source->site_id,
                    'fiscal_year_id' => $targetFY->id,
                    'account_code' => $source->account_code,
                    'abbreviation' => $source->abbreviation,
                    'account_name' => $source->account_name,
                    'short_description' => $source->short_description,
                    'account_type' => $source->account_type,
                    'parent_account_id' => $newParentId,
                    'hierarchy_level' => $source->hierarchy_level,
                    'is_active' => false, // New year accounts start inactive
                    'initial_budget' => 0, // Reset budget - must be set explicitly
                    'budget_control' => $source->budget_control,
                    'category' => $source->category,
                    'sub_category' => $source->sub_category,
                    'typical_usage' => $source->typical_usage,
                    'tax_applicable' => $source->tax_applicable,
                ]);

                // Track for parent resolution
                $codeToNewId[$source->site_id][$source->account_code] =
                    $copy->id;
                $copiedCount++;
            }

            Log::info('COA structure copied', [
                'source_fy' => $sourceFY->year,
                'target_fy' => $targetFY->year,
                'accounts_copied' => $copiedCount,
            ]);

            return $copiedCount;
        });
    }

    /**
     * Calculate budget metrics for a fiscal year.
     *
     * @return array{allocated: float, committed: float, realized: float, available: float, utilization_rate: float}
     */
    public function calculateBudgetMetrics(FiscalYear $fiscalYear): array
    {
        // Get total allocated budget from COA accounts
        $allocated = CoaAccount::where('fiscal_year_id', $fiscalYear->id)
            ->where('is_active', true)
            ->sum('initial_budget');

        // Get total committed (COMMITTED status only)
        $committed = BudgetCommitment::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', BudgetCommitment::STATUS_COMMITTED)
            ->sum('amount');

        // Realized = spent/actual - from payment requests in this FY
        $realized = PaymentRequest::whereHas('splits.program', function (
            $q,
        ) use ($fiscalYear) {
            $q->where('fiscal_year_id', $fiscalYear->id);
        })
            ->where('status', 'APPROVED')
            ->sum('total_amount');

        // Available = Allocated - Committed
        $available = $allocated - $committed;

        // Utilization rate
        $utilizationRate = $allocated > 0 ? ($committed / $allocated) * 100 : 0;

        return [
            'allocated' => (float) $allocated,
            'committed' => (float) $committed,
            'realized' => (float) $realized,
            'available' => (float) $available,
            'utilization_rate' => round($utilizationRate, 2),
        ];
    }

    /**
     * Close a fiscal year with selected actions.
     */
    public function close(FiscalYear $fiscalYear, array $data): void
    {
        DB::transaction(function () use ($fiscalYear, $data) {
            $options = $data['options'] ?? [];
            $notes = $data['notes'] ?? null;

            // Pre-close validation counts
            $validationData = $this->getPreCloseValidation($fiscalYear);

            // Check for active commitments
            $activeCommitments = BudgetCommitment::where(
                'fiscal_year_id',
                $fiscalYear->id,
            )
                ->where('status', BudgetCommitment::STATUS_COMMITTED)
                ->count();

            if (
                $activeCommitments > 0 &&
                ! ($options['release_uncommitted_funds'] ?? false)
            ) {
                throw new \RuntimeException(
                    "Cannot close fiscal year with {$activeCommitments} active commitments. ".
                        'Enable "release_uncommitted_funds" option to proceed.',
                );
            }

            // Release uncommitted funds if requested
            if ($options['release_uncommitted_funds'] ?? false) {
                $this->releaseUncommittedFunds($fiscalYear);
            }

            // Execute selected actions
            if ($options['archive_completed_programs'] ?? false) {
                $this->archiveCompletedPrograms($fiscalYear);
            }

            // Generate year-end report if requested
            $reportPath = null;
            if ($options['generate_report'] ?? false) {
                $reportPath = $this->reportGenerator->generateYearEndReport(
                    $fiscalYear,
                );
            }

            // Mark fiscal year as closed
            $fiscalYear->update(['is_closed' => true]);

            // Log the closure
            $this->logClosure(
                $fiscalYear,
                $options,
                $notes,
                $validationData,
                $reportPath,
            );

            // Send notifications if requested
            if ($options['send_notifications'] ?? false) {
                $this->sendClosureNotifications($fiscalYear);
            }
        });
    }

    /**
     * Release all uncommitted funds for a fiscal year.
     */
    protected function releaseUncommittedFunds(FiscalYear $fiscalYear): int
    {
        return BudgetCommitment::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', BudgetCommitment::STATUS_COMMITTED)
            ->update(['status' => BudgetCommitment::STATUS_RELEASED]);
    }

    /**
     * Reopen a closed fiscal year.
     */
    public function reopen(FiscalYear $fiscalYear, string $reason): void
    {
        DB::transaction(function () use ($fiscalYear, $reason) {
            $fiscalYear->update(['is_closed' => false]);

            Log::info('Fiscal year reopened', [
                'fiscal_year' => $fiscalYear->year,
                'user_id' => auth()->id(),
                'reason' => $reason,
                'timestamp' => now(),
            ]);
        });
    }

    /**
     * Get pre-close validation data.
     */
    public function getPreCloseValidation(FiscalYear $fiscalYear): array
    {
        return [
            'active_programs' => Program::where(
                'fiscal_year_id',
                $fiscalYear->id,
            )
                ->where('status', Program::STATUS_ACTIVE)
                ->count(),
            'pending_payment_requests' => PaymentRequest::whereHas(
                'splits.program',
                function ($q) use ($fiscalYear) {
                    $q->where('fiscal_year_id', $fiscalYear->id);
                },
            )
                ->where('status', 'PENDING')
                ->count(),
            'active_commitments' => BudgetCommitment::where(
                'fiscal_year_id',
                $fiscalYear->id,
            )
                ->where('status', BudgetCommitment::STATUS_COMMITTED)
                ->count(),
            'coa_account_count' => CoaAccount::where(
                'fiscal_year_id',
                $fiscalYear->id,
            )->count(),
        ];
    }

    /**
     * Archive all completed programs for a fiscal year.
     */
    protected function archiveCompletedPrograms(FiscalYear $fiscalYear): int
    {
        return Program::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', Program::STATUS_COMPLETED)
            ->update(['status' => Program::STATUS_ARCHIVED]);
    }

    /**
     * Log the closure action with audit trail.
     */
    protected function logClosure(
        FiscalYear $fiscalYear,
        array $options,
        ?string $notes,
        array $validationData,
        ?string $reportPath = null,
    ): void {
        Log::info('Fiscal year closed', [
            'fiscal_year' => $fiscalYear->year,
            'user_id' => auth()->id(),
            'options' => $options,
            'notes' => $notes,
            'validation_data' => $validationData,
            'report_path' => $reportPath,
            'timestamp' => now(),
        ]);
    }

    /**
     * Send closure notifications to relevant users.
     */
    protected function sendClosureNotifications(FiscalYear $fiscalYear): void
    {
        $pis = Program::where('fiscal_year_id', $fiscalYear->id)
            ->with('researchAssociate')
            ->get()
            ->pluck('researchAssociate')
            ->unique()
            ->filter();

        foreach ($pis as $pi) {
            $pi->notify(new FiscalYearClosedNotification($fiscalYear));
        }
    }
}
