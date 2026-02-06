<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\CoaAccount;
use App\Models\Program;
use App\Models\RevenueHarvest;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RevenueController extends Controller
{
    public function createHarvest(Site $site)
    {
        return Inertia::render('revenue/harvest/create', [
            'site_code' => $site->site_code,
            'programs' => Program::where('site_id', $site->id)
                ->where('status', Program::STATUS_ACTIVE)
                ->select('id', 'program_code', 'program_name', 'classification')
                ->get(),
            'buyers' => Buyer::where('is_active', true)->select('id', 'name')->get(),
            'coa_accounts' => CoaAccount::where('site_id', $site->id)
                ->where('account_type', 'REVENUE')
                ->where('is_active', true)
                ->select('id', 'account_code', 'account_name')
                ->get(),
        ]);
    }

    public function storeHarvest(Request $request, Site $site)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'harvest_date' => 'required|date',
            'harvest_cycle' => 'required|integer|min:1',
            'crop_type' => 'required|string|max:100',
            'quantity_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'buyer_id' => 'required|exists:buyers,id',
            'payment_method' => 'required|string|in:CASH,BANK_TRANSFER',
            'payment_date' => 'required|date',
            'bank_reference' => 'nullable|string|max:100',
            'coa_account_id' => 'required|exists:coa_accounts,id',
            'notes' => 'nullable|string',
        ]);

        $validated['site_id'] = $site->id;
        $validated['total_revenue'] = $validated['quantity_kg'] * $validated['price_per_kg'];
        $validated['created_by'] = Auth::id();
        $validated['status'] = RevenueHarvest::STATUS_POSTED;

        $validated['harvest_number'] = 'HRV-'.strtoupper(Str::random(8));

        RevenueHarvest::create($validated);

        return redirect()->route('programs.index', $site->site_code)->with('success', 'Harvest revenue recorded successfully.');
    }

    public function createTesting(Site $site)
    {
        // Placeholder for testing service revenue form
        return Inertia::render('revenue/testing/create', [
            'site_code' => $site->site_code,
        ]);
    }

    public function storeTesting(Request $request, Site $site)
    {
        // Placeholder for testing service revenue storage
    }
}
