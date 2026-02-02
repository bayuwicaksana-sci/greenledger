<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoaAccountRequest;
use App\Http\Requests\UpdateCoaAccountRequest;
use App\Models\CoaAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CoaAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CoaAccount::query()->with('site');

        if ($request->has('site_id')) {
            $query->where('site_id', $request->input('site_id'));
        }

        // Standard COA sorting: code is usually hierarchical
        $query->orderBy('account_code', 'asc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")->orWhere(
                    'account_name',
                    'like',
                    "%{$search}%",
                );
            });
        }

        $accounts = $query->paginate(10)->withQueryString();
        $sites = \App\Models\Site::all();

        return Inertia::render('config/coa/Index', [
            'accounts' => $accounts,
            'filters' => $request->only(['search', 'site_id']),
            'sites' => $sites,
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
                'hierarchy_level',
            )
                ->where('is_active', true)
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCoaAccountRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['parent_account_id'])) {
            $parent = CoaAccount::find($data['parent_account_id']);
            $data['hierarchy_level'] = $parent
                ? $parent->hierarchy_level + 1
                : 1;
        } else {
            $data['hierarchy_level'] = 1;
        }

        CoaAccount::create($data);

        return redirect()
            ->back()
            ->with('success', 'COA Account created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CoaAccount $coa)
    {
        return Inertia::render('config/coa/Edit', [
            'account' => $coa,
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
            if (!empty($data['parent_account_id'])) {
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
        // Check if there are related transactions before deleting
        // For now, we'll just delete. In production this should safeguard data.
        $coa->delete();

        return redirect()
            ->back()
            ->with('success', 'COA Account deleted successfully.');
    }
}
