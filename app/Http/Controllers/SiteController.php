<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    /**
     * Display a listing of active sites.
     */
    public function index(): JsonResponse
    {
        $sites = Site::where('is_active', true)
            ->orderBy('site_name')
            ->get(['id', 'site_code', 'site_name', 'address', 'contact_info', 'is_active']);

        return response()->json([
            'data' => $sites,
        ]);
    }
}
