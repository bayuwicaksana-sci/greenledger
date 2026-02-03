<?php

namespace App\Http\Controllers;

use App\Services\CoaAccountTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoaAccountTemplateController extends Controller
{
    public function __construct(
        protected CoaAccountTemplateService $templateService,
    ) {}

    /**
     * List available templates with conflict info for a given site.
     */
    public function index(Request $request): JsonResponse
    {
        $siteId = $request->input('site_id');
        $templates = $this->templateService->getTemplates();

        $result = [];
        foreach ($templates as $key => $template) {
            $entry = [
                'key' => $key,
                'name' => $template['name'],
                'description' => $template['description'] ?? '',
                'account_count' => count($template['accounts']),
                'accounts' => $template['accounts'],
            ];

            if ($siteId) {
                $conflicts = $this->templateService->checkConflicts($key, $siteId);
                $entry['conflicts'] = $conflicts;
                $entry['has_conflicts'] = in_array(true, $conflicts, true);
            }

            $result[] = $entry;
        }

        return response()->json($result);
    }

    /**
     * Apply a template to a site.
     */
    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_key' => ['required', 'string'],
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'skip_existing' => ['boolean'],
        ]);

        try {
            $count = $this->templateService->applyTemplate(
                $validated['template_key'],
                $validated['site_id'],
                $validated['skip_existing'] ?? false,
            );

            return response()->json([
                'success' => true,
                'message' => "{$count} account(s) created from template.",
                'count' => $count,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
