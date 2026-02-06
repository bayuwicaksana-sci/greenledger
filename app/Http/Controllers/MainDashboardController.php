<?php

namespace App\Http\Controllers;

use App\Models\ApprovalInstance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class MainDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('main-dashboard', [
            'pendingApprovals' => $this->getPendingApprovals($request->user()),
        ]);
    }

    /**
     * Retrieve the most recent approval instances awaiting action from the given user.
     */
    private function getPendingApprovals(User $user): Collection
    {
        return ApprovalInstance::query()
            ->with(['approvable.site', 'currentStep', 'submittedBy', 'workflow'])
            ->where('status', \App\States\ApprovalInstance\InProgress::class)
            ->whereHas('currentStep', function ($query) use ($user) {
                $query
                    ->where(function ($q) use ($user) {
                        $q->where('approver_type', 'user')->whereJsonContains(
                            'approver_identifiers',
                            $user->id,
                        );
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('approver_type', 'role')->where(function ($subQ) use ($user) {
                            foreach ($user->roles as $role) {
                                $subQ->orWhereJsonContains('approver_identifiers', $role->name);
                            }
                        });
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('approver_type', 'permission')->where(function ($subQ) use ($user) {
                            foreach ($user->getAllPermissions() as $perm) {
                                $subQ->orWhereJsonContains('approver_identifiers', $perm->name);
                            }
                        });
                    });
            })
            ->latest()
            ->take(10)
            ->get();
    }
}
