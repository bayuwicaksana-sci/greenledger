<?php

namespace App\Http\Controllers;

use App\Http\Requests\Site\StoreSiteRequest;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    /**
     * Display a listing of research stations.
     */
    public function index(): Response
    {
        $sites = Site::where('is_active', true)
            ->withCount(['users', 'programs', 'coaAccounts'])
            ->orderBy('site_name')
            ->get();

        return Inertia::render('research-stations/index', [
            'sites' => $sites,
        ]);
    }

    /**
     * Show the form for creating a new site.
     */
    public function create(): Response
    {
        return Inertia::render('research-stations/create');
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(StoreSiteRequest $request)
    {
        Site::create($request->validated());

        return redirect()
            ->route('research-stations.index')
            ->with('success', 'Site created successfully.');
    }

    /**
     * Generate site code suggestion from site name.
     */
    public function suggestCode(Request $request): JsonResponse
    {
        $siteName = $request->input('site_name', '');

        // Extract words from site name
        $words = preg_split('/\s+/', trim($siteName));

        // Generate code from first letter of first 3 words
        if (count($words) >= 3) {
            $code = strtoupper(
                substr($words[0], 0, 1).
                substr($words[1], 0, 1).
                substr($words[2], 0, 1),
            );
        } elseif (count($words) === 1 && strlen($words[0]) >= 3) {
            // Single word: take first 3 letters
            $code = strtoupper(substr($words[0], 0, 3));
        } else {
            // Less than 3 characters: pad with 'X'
            $code = strtoupper(str_pad(
                substr(implode('', $words), 0, 3),
                3,
                'X',
            ));
        }

        // Check availability
        $isAvailable = ! Site::where('site_code', $code)->exists();

        return response()->json([
            'suggested_code' => $code,
            'is_available' => $isAvailable,
        ]);
    }

    /**
     * Display a listing of active sites (API endpoint).
     */
    public function apiIndex(): JsonResponse
    {
        $sites = Site::where('is_active', true)
            ->orderBy('site_name')
            ->get(['id', 'site_code', 'site_name', 'address', 'contact_info', 'is_active']);

        return response()->json([
            'data' => $sites,
        ]);
    }
}
