<?php

namespace App\Services;

use App\Models\FiscalYear;
use App\Models\PaymentRequest;
use App\Models\Program;
use App\Notifications\FiscalYearClosedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FiscalYearService
{
    /**
     * Close a fiscal year with selected actions.
     */
    public function close(FiscalYear $fiscalYear, array $data): void
    {
        DB::transaction(function () use ($fiscalYear, $data) {
            // Extract options and notes
            $options = $data['options'] ?? [];
            $notes = $data['notes'] ?? null;

            // Pre-close validation counts
            $validationData = $this->getPreCloseValidation($fiscalYear);

            // Execute selected actions
            if ($options['archive_completed_programs'] ?? false) {
                $this->archiveCompletedPrograms($fiscalYear);
            }

            // Mark fiscal year as closed
            $fiscalYear->update(['is_closed' => true]);

            // Log the closure
            $this->logClosure($fiscalYear, $options, $notes, $validationData);

            // Send notifications if requested
            if ($options['send_notifications'] ?? false) {
                $this->sendClosureNotifications($fiscalYear);
            }
        });
    }

    /**
     * Reopen a closed fiscal year.
     */
    public function reopen(FiscalYear $fiscalYear, string $reason): void
    {
        DB::transaction(function () use ($fiscalYear, $reason) {
            $fiscalYear->update(['is_closed' => false]);

            // Log the reopen action
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
            'active_programs' => Program::where('fiscal_year', $fiscalYear->year)
                ->where('status', Program::STATUS_ACTIVE)
                ->count(),
            'pending_payment_requests' => PaymentRequest::whereHas('splits', function ($query) use ($fiscalYear) {
                $query->whereHas('program', function ($q) use ($fiscalYear) {
                    $q->where('fiscal_year', $fiscalYear->year);
                });
            })
                ->where('status', 'PENDING')
                ->count(),
            'unsettled_transactions' => 0, // TODO: Implement when settlement tracking is available
        ];
    }

    /**
     * Archive all completed programs for a fiscal year.
     */
    protected function archiveCompletedPrograms(FiscalYear $fiscalYear): int
    {
        return Program::where('fiscal_year', $fiscalYear->year)
            ->where('status', Program::STATUS_COMPLETED)
            ->update(['status' => Program::STATUS_ARCHIVED]);
    }

    /**
     * Generate year-end report (placeholder for future implementation).
     */
    public function generateYearEndReport(FiscalYear $fiscalYear): ?string
    {
        // TODO: Implement PDF generation in Phase 4
        return null;
    }

    /**
     * Log the closure action with audit trail.
     */
    protected function logClosure(FiscalYear $fiscalYear, array $options, ?string $notes, array $validationData): void
    {
        Log::info('Fiscal year closed', [
            'fiscal_year' => $fiscalYear->year,
            'user_id' => auth()->id(),
            'options' => $options,
            'notes' => $notes,
            'validation_data' => $validationData,
            'timestamp' => now(),
        ]);
    }

    /**
     * Send closure notifications to relevant users.
     */
    protected function sendClosureNotifications(FiscalYear $fiscalYear): void
    {
        // Get PIs of programs in this fiscal year
        $pis = Program::where('fiscal_year', $fiscalYear->year)
            ->with('researchAssociate')
            ->get()
            ->pluck('researchAssociate')
            ->unique()
            ->filter();

        foreach ($pis as $pi) {
            $pi->notify(new FiscalYearClosedNotification($fiscalYear));
        }

        // TODO: Also notify Managers and AVPs
    }
}
