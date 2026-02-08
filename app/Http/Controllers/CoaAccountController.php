<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkStoreCoaAccountRequest;
use App\Http\Requests\StoreCoaAccountRequest;
use App\Http\Requests\UpdateCoaAccountRequest;
use App\Models\ApprovalInstance;
use App\Models\ApprovalWorkflow;
use App\Models\CoaAccount;
use App\Models\FiscalYear;
use App\Services\Approval\WorkflowEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CoaAccountController extends Controller
{
    public function __construct(protected WorkflowEngine $workflowEngine) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get fiscal year parameter - can be year integer or fiscal_year_id
        $fiscalYearParam = $request->input('fiscal_year');

        if ($fiscalYearParam) {
            // Try to find by ID first, then by year
            $fiscalYear =
                FiscalYear::find($fiscalYearParam) ??
                FiscalYear::where('year', $fiscalYearParam)->first();
        } else {
            // Default to current open fiscal year
            $fiscalYear = FiscalYear::where('is_closed', false)
                ->orderByDesc('year')
                ->first();
        }

        if (! $fiscalYear) {
            // Fallback to latest fiscal year
            $fiscalYear = FiscalYear::orderByDesc('year')->first();
        }

        $fiscalYearId = $fiscalYear?->id;

        $accounts = CoaAccount::query()
            ->with([
                'site',
                'fiscalYear',
                'approvalInstances' => fn ($q) => $q->latest(),
            ])
            ->where('fiscal_year_id', $fiscalYearId)
            ->select('coa_accounts.*')
            ->selectRaw(
                'COALESCE((
                    SELECT cba.budget_amount
                    FROM coa_budget_allocations cba
                    WHERE cba.coa_account_id = coa_accounts.id AND cba.fiscal_year_id = ?
                ), 0) AS allocated_budget',
                [$fiscalYearId],
            )
            ->selectRaw(
                'CASE
                    WHEN account_type = ? THEN
                        COALESCE((SELECT SUM(prs.split_amount)
                                  FROM payment_request_splits prs
                                  INNER JOIN programs p ON p.id = prs.program_id
                                  WHERE prs.coa_account_id = coa_accounts.id AND p.fiscal_year_id = ?
                        ), 0)
                    WHEN account_type = ? THEN
                        COALESCE((SELECT SUM(rh.total_revenue)
                                  FROM revenue_harvest rh
                                  INNER JOIN programs p ON p.id = rh.program_id
                                  WHERE rh.coa_account_id = coa_accounts.id AND p.fiscal_year_id = ?
                        ), 0)
                        + COALESCE((SELECT SUM(rts.contract_value)
                                    FROM revenue_testing_services rts
                                    INNER JOIN programs p ON p.id = rts.program_id
                                    WHERE rts.coa_account_id = coa_accounts.id AND p.fiscal_year_id = ?
                        ), 0)
                    ELSE 0
                END AS actual_amount',
                [
                    'EXPENSE',
                    $fiscalYearId,
                    'REVENUE',
                    $fiscalYearId,
                    $fiscalYearId,
                ],
            )
            ->orderBy('account_code', 'asc')
            ->get()
            ->append('balance');

        $accounts->each(function (CoaAccount $account) {
            $instance = $account->approvalInstances->first();
            $account->approval_status = $instance
                ? $this->resolveApprovalStatus($instance)
                : null;
        });

        return Inertia::render('config/coa/Index', [
            'accounts' => $accounts,
            'sites' => \App\Models\Site::all(),
            'fiscalYears' => FiscalYear::orderByDesc('year')->get([
                'id',
                'year',
                'is_closed',
            ]),
            'selectedFiscalYear' => $fiscalYear?->year,
            'selectedFiscalYearId' => $fiscalYear?->id,
            'currentFiscalYearId' => FiscalYear::where('is_closed', false)
                ->orderByDesc('year')
                ->value('id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get current open fiscal year or requested one
        $selectedFiscalYearId =
            $request->get('fiscal_year_id') ??
            FiscalYear::where('is_closed', false)
                ->orderByDesc('year')
                ->value('id');

        return Inertia::render('config/coa/Create', [
            'sites' => \App\Models\Site::all(),
            'fiscalYears' => FiscalYear::orderByDesc('year')->get([
                'id',
                'year',
                'is_closed',
            ]),
            'selectedFiscalYearId' => $selectedFiscalYearId,
            'standardAccounts' => config(
                'coa_templates.standard_agricultural.accounts',
                [],
            ),
            'parents' => CoaAccount::select(
                'id',
                'account_code',
                'account_name',
                'account_type',
                'site_id',
                'fiscal_year_id',
                'hierarchy_level',
            )
                ->where('fiscal_year_id', $selectedFiscalYearId)
                ->where('is_active', true)
                ->get(),
            'existingCodes' => CoaAccount::select('site_id', 'account_code')
                ->where('fiscal_year_id', $selectedFiscalYearId)
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCoaAccountRequest $request)
    {
        $data = $request->validated();

        if (! empty($data['parent_account_id'])) {
            $parent = CoaAccount::find($data['parent_account_id']);
            $data['hierarchy_level'] = $parent
                ? $parent->hierarchy_level + 1
                : 1;
        } else {
            $data['hierarchy_level'] = 1;
        }

        $data['created_by'] = $request->user()->id;
        $data['is_active'] = false;

        $account = CoaAccount::create($data);

        $workflow = ApprovalWorkflow::active()
            ->forModel(CoaAccount::class)
            ->first();
        if ($workflow) {
            $instance = $this->workflowEngine->initializeWorkflow(
                $account,
                $workflow,
                $request->user(),
            );
            $this->workflowEngine->submitForApproval(
                $instance,
                $request->user(),
            );
        }

        return redirect()
            ->route('config.coa.index')
            ->with('success', 'COA Account created successfully.');
    }

    /**
     * Store multiple accounts in a single transaction.
     */
    public function bulkStore(BulkStoreCoaAccountRequest $request)
    {
        $accounts = $request->validated()['accounts'];

        try {
            DB::beginTransaction();

            // Map to store temp_id -> actual created account ID
            $tempIdMap = [];

            // Sort accounts so parents are created before children
            $sortedAccounts = $this->sortAccountsByDependency($accounts);

            foreach ($sortedAccounts as $accountData) {
                // Store temp_id before removing it
                $tempId = $accountData['_temp_id'] ?? null;

                // Resolve parent_temp_id to actual parent_account_id
                if (
                    ! empty($accountData['parent_temp_id']) &&
                    isset($tempIdMap[$accountData['parent_temp_id']])
                ) {
                    $accountData['parent_account_id'] =
                        $tempIdMap[$accountData['parent_temp_id']];
                }

                // Remove temp fields before creating
                unset($accountData['parent_temp_id']);
                unset($accountData['_temp_id']);

                // Calculate hierarchy level
                if (! empty($accountData['parent_account_id'])) {
                    $parent = CoaAccount::find(
                        $accountData['parent_account_id'],
                    );
                    $accountData['hierarchy_level'] = $parent
                        ? $parent->hierarchy_level + 1
                        : 1;
                } else {
                    $accountData['hierarchy_level'] = 1;
                }

                $accountData['created_by'] = $request->user()->id;
                $accountData['is_active'] = false;

                // Create the account
                $account = CoaAccount::create($accountData);

                // Trigger approval workflow if active
                $workflow = ApprovalWorkflow::active()
                    ->forModel(CoaAccount::class)
                    ->first();
                if ($workflow) {
                    $instance = $this->workflowEngine->initializeWorkflow(
                        $account,
                        $workflow,
                        $request->user(),
                    );
                    $this->workflowEngine->submitForApproval(
                        $instance,
                        $request->user(),
                    );
                }

                // Store mapping if temp_id exists
                if ($tempId) {
                    $tempIdMap[$tempId] = $account->id;
                }
            }

            DB::commit();

            $count = count($accounts);

            return redirect()
                ->route('config.coa.index')
                ->with('success', "{$count} account(s) created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to create accounts: '.$e->getMessage(),
                );
        }
    }

    /**
     * Sort accounts so parents are created before children.
     */
    private function sortAccountsByDependency(array $accounts): array
    {
        $sorted = [];
        $tempIdMap = [];

        // First, index all accounts by their temp_id
        foreach ($accounts as $account) {
            $tempId = $account['_temp_id'] ?? null;
            if ($tempId) {
                $tempIdMap[$tempId] = $account;
            }
        }

        // Add accounts without parent_temp_id first
        foreach ($accounts as $account) {
            if (empty($account['parent_temp_id'])) {
                $sorted[] = $account;
            }
        }

        // Add accounts with parent_temp_id
        foreach ($accounts as $account) {
            if (! empty($account['parent_temp_id'])) {
                $sorted[] = $account;
            }
        }

        return $sorted;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CoaAccount $coa)
    {
        $instance = $coa->approvalInstances()->latest()->first();
        $coa->load('fiscalYear');

        return Inertia::render('config/coa/Edit', [
            'account' => array_merge(
                $coa->only([
                    'id',
                    'site_id',
                    'account_code',
                    'account_name',
                    'abbreviation',
                    'account_type',
                    'short_description',
                    'parent_account_id',
                    'is_active',
                    'initial_budget',
                    'first_transaction_at',
                    'category',
                    'sub_category',
                    'typical_usage',
                    'tax_applicable',
                ]),
                [
                    'fiscal_year' => $coa->fiscalYear->only([
                        'id',
                        'year',
                        'is_closed',
                    ]),
                    'approval_status' => $instance
                        ? $this->resolveApprovalStatus($instance)
                        : null,
                ],
            ),
            'sites' => \App\Models\Site::all(),
            'parents' => CoaAccount::select(
                'id',
                'account_code',
                'account_name',
                'account_type',
                'hierarchy_level',
            )
                ->where('fiscal_year_id', $coa->fiscal_year_id)
                ->where('is_active', true)
                ->where('id', '!=', $coa->id) // Prevent self-parenting
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCoaAccountRequest $request, CoaAccount $coa)
    {
        $data = $request->validated();

        // If parent_account_id is strictly provided (even if null)
        if (array_key_exists('parent_account_id', $data)) {
            if (! empty($data['parent_account_id'])) {
                $parent = CoaAccount::find($data['parent_account_id']);
                $data['hierarchy_level'] = $parent
                    ? $parent->hierarchy_level + 1
                    : 1;
            } else {
                $data['hierarchy_level'] = 1;
            }
        }

        $data['updated_by'] = $request->user()->id;

        $coa->update($data);

        return redirect()
            ->back()
            ->with('success', 'COA Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CoaAccount $coa)
    {
        // Check for transactions before allowing deletion
        $transactionCount = $coa->getTransactionCount();

        if ($transactionCount > 0) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    "Cannot delete account with {$transactionCount} transaction(s). Please deactivate the account instead.",
                );
        }

        // Check if there are child accounts
        if ($coa->childAccounts()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Cannot delete account with child accounts. Please remove or reassign child accounts first.',
                );
        }

        $coa->delete();

        return redirect()
            ->back()
            ->with('success', 'COA Account deleted successfully.');
    }

    /**
     * Map an ApprovalInstance state to a frontend-friendly status string.
     */
    private function resolveApprovalStatus(ApprovalInstance $instance): string
    {
        return match (true) {
            $instance->status instanceof \App\States\ApprovalInstance\Approved => 'approved',
            $instance->status instanceof \App\States\ApprovalInstance\Rejected => 'rejected',
            $instance->status instanceof \App\States\ApprovalInstance\Cancelled => 'cancelled',
            $instance->status instanceof \App\States\ApprovalInstance\InProgress => 'pending_approval',
            $instance->status instanceof \App\States\ApprovalInstance\ChangesRequested => 'pending_approval',
            $instance->status instanceof \App\States\ApprovalInstance\Pending => 'draft',
            default => 'draft',
        };
    }

    /**
     * Get the next available account code.
     */
    public function nextCode(Request $request)
    {
        $siteId = $request->input('site_id');
        $parentId = $request->input('parent_id');

        if (! $siteId) {
            return response()->json(['code' => '']);
        }

        $query = CoaAccount::where('site_id', $siteId);

        if ($parentId) {
            $parent = CoaAccount::find($parentId);
            if (! $parent || ! is_numeric($parent->account_code)) {
                return response()->json(['code' => '']);
            }

            $query->where('parent_account_id', $parentId);
            $maxCode = $query->max('account_code');
            $pCode = (int) $parent->account_code;

            // Determine step based on parent's level (trailing zeros)
            $step = 1;
            if ($pCode % 1000 == 0) {
                // Parent is Root (Level 1, e.g. 5000). Children are Level 2 (step 100: 5100, 5200)
                $step = 100;
            } elseif ($pCode % 100 == 0) {
                // Parent is Level 2 (e.g. 5100). Children are Level 3 (step 10: 5110, 5120)
                $step = 10;
            } else {
                // Parent is Level 3 (e.g. 5110). Children are Level 4 (step 1: 5111, 5112)
                $step = 1;
            }

            if ($maxCode && is_numeric($maxCode)) {
                // Sibling exists, increment from max sibling
                return response()->json([
                    'code' => (string) ($maxCode + $step),
                ]);
            } else {
                // First child, increment from parent
                return response()->json(['code' => (string) ($pCode + $step)]);
            }
        } else {
            // Root level (Level 1). Step 1000 (1000, 2000, 3000)
            $query->whereNull('parent_account_id');
            $maxCode = $query->max('account_code');
            $step = 1000;

            if ($maxCode && is_numeric($maxCode)) {
                // Check if we are following standard x000 format
                if ($maxCode % 1000 == 0) {
                    return response()->json([
                        'code' => (string) ($maxCode + $step),
                    ]);
                }
                // If weird non-standard roots exist, just +1000 safely or +1?
                // Let's stick to +1000 to encourage structure.
                // Actually, if max is 1005, we probably want 2000, not 2005.
                // Let's round up to next thousand.
                $next = ceil(($maxCode + 1) / 1000) * 1000;

                return response()->json(['code' => (string) $next]);
            } else {
                return response()->json(['code' => '1000']);
            }
        }
    }
}
