<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $site_code = $request->route('site')->site_code;

        $findSite = Site::whereSiteCode($site_code)->first(['id']);

        if (! $findSite) {
            return response('Not Found', 404);
        }

        // Check if user has access to this site
        $hasAccess = $request->user()->sites->contains('id', $findSite->id);

        if ($hasAccess) {
            return $next($request);
        }

        return abort(403, 'You do not have access to this research station.');
    }
}
