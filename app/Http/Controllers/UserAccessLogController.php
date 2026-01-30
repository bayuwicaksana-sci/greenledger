<?php

namespace App\Http\Controllers;

use App\Models\UserAccessLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAccessLogController extends Controller
{
    /**
     * Get access logs for all users or a specific user.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserAccessLog::with('user');

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }

        // Search by IP
        if ($request->has('ip_address')) {
            $query->where('ip_address', 'like', "%{$request->input('ip_address')}%");
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 50));

        return response()->json($logs);
    }
}
