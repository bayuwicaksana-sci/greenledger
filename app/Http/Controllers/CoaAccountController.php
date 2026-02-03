<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkStoreCoaAccountRequest;
use App\Http\Requests\StoreCoaAccountRequest;
use App\Http\Requests\UpdateCoaAccountRequest;
use App\Models\CoaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CoaAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $accounts = CoaAccount::query()
            ->with('site')
            ->select('coa_accounts.*')
            ->selectRaw('CASE
                WHEN coa_accounts.account_type = ? THEN
                    COALESCE((SELECT SUM(split_amount) FROM payment_request_splits WHERE payment_request_splits.coa_account_id = coa_accounts.id), 0)
                WHEN coa_accounts.account_type = ? THEN
                    COALESCE((SELECT SUM(total_revenue) FROM revenue_harvest WHERE revenue_harvest.coa_account_id = coa_accounts.id), 0)
                    + COALESCE((SELECT SUM(contract_value) FROM revenue_testing_services WHERE revenue_testing_services.coa_account_id = coa_accounts.id), 0)
                ELSE 0
            END AS actual_amount', ['EXPENSE', 'REVENUE'])
            ->orderBy('account_code', 'asc')
            ->get();

        return Inertia::render('config/coa/Index', [
            'accounts' => $accounts,
            'sites' => \App\Models\Site::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('config/coa/Create', [
            'sites' => \App\Models\Site::all(),
            'parents' => CoaAccount::select(
                'id',
                'account_code',
                'account_name',
                'site_id',
                'hierarchy_level',
            )
                ->where('is_active', true)
                ->get(),
            'existingCodes' => CoaAccount::select(
                'site_id',
                'account_code',
            )->get(),
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

        CoaAccount::create($data);

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

                // Create the account
                $account = CoaAccount::create($accountData);

                // Store mapping if temp_id exists
                if ($tempId) {
                    $tempIdMap[$tempId] = $account->id;
                }
            }

            DB::commit();

            $count = count($accounts);

            return redirect()
                ->route('config.coa.index')
                ->with(
                    'success',
                    "{$count} account(s) created successfully.",
                );
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
        return Inertia::render('config/coa/Edit', [
            'account' => $coa->only([
                'id',
                'site_id',
                'account_code',
                'account_name',
                'account_type',
                'short_description',
                'parent_account_id',
                'is_active',
                'initial_budget',
                'first_transaction_at',
            ]),
            'sites' => \App\Models\Site::all(),
            'parents' => CoaAccount::select(
                'id',
                'account_code',
                'account_name',
                'hierarchy_level',
            )
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
}
