<?php

namespace App\Services;

use App\Models\CoaAccount;
use Illuminate\Support\Facades\DB;

class CoaAccountTemplateService
{
    /**
     * Get all available templates with their metadata.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getTemplates(): array
    {
        return config('coa_templates', []);
    }

    /**
     * Check which accounts from a template already exist for a given site.
     *
     * @return array<string, bool> Map of account_code -> exists
     */
    public function checkConflicts(string $templateKey, int $siteId): array
    {
        $templates = $this->getTemplates();
        $template = $templates[$templateKey] ?? null;

        if (! $template) {
            return [];
        }

        $conflicts = [];
        foreach ($template['accounts'] as $account) {
            // New logic: Check explicit base code
            $baseCode = $account['code'];

            $conflicts[$account['code']] = CoaAccount::where('site_id', $siteId)
                ->where('account_code', $baseCode)
                ->exists();
        }

        return $conflicts;
    }

    /**
     * Apply a template to a site, creating all accounts in the correct hierarchy order.
     *
     * @return int Number of accounts created.
     *
     * @throws \InvalidArgumentException If template does not exist or site has conflicts.
     */
    public function applyTemplate(
        string $templateKey,
        int $siteId,
        bool $skipExisting = false,
        ?int $createdBy = null,
    ): int {
        $templates = $this->getTemplates();
        $template = $templates[$templateKey] ?? null;

        if (! $template) {
            throw new \InvalidArgumentException(
                "Template '{$templateKey}' not found.",
            );
        }

        $conflicts = $this->checkConflicts($templateKey, $siteId);
        $hasConflicts = in_array(true, $conflicts, true);

        if ($hasConflicts && ! $skipExisting) {
            throw new \InvalidArgumentException(
                'Template has conflicting accounts. Use skipExisting to skip them.',
            );
        }

        // Fetch site code for prefixing
        $site = \App\Models\Site::find($siteId);
        $siteCode = $site ? $site->site_code : 'UNK';

        return DB::transaction(function () use (
            $template,
            $siteId,
            $skipExisting,
            $createdBy,
        ) {
            $createdCount = 0;
            $codeToId = [];

            // Sort: accounts without parents first, then those with parents
            $sorted = $this->sortTemplateAccounts($template['accounts']);

            foreach ($sorted as $account) {
                // New logic: Use explicit columns
                $baseCode = $account['code']; // e.g. 5211

                $abbr = $account['abbreviation'] ?? null;

                // Check if exists
                $exists = CoaAccount::where('site_id', $siteId)
                    ->where('account_code', $baseCode)
                    ->exists();

                if ($exists) {
                    if ($skipExisting) {
                        // Find existing to link children
                        $existing = CoaAccount::where('site_id', $siteId)
                            ->where('account_code', $baseCode)
                            ->first();
                        if ($existing) {
                            $codeToId[$baseCode] = $existing->id;
                        }

                        continue;
                    } else {
                        throw new \InvalidArgumentException(
                            "Account code {$baseCode} already exists.",
                        );
                    }
                }

                $parentId = null;
                $hierarchyLevel = 1;

                // Parent Handling
                // 'parent' in config is '1000' (base code of parent).
                if (
                    $account['parent'] &&
                    isset($codeToId[$account['parent']])
                ) {
                    $parentId = $codeToId[$account['parent']];
                    $parentModel = CoaAccount::find($parentId);
                    $hierarchyLevel = $parentModel
                        ? $parentModel->hierarchy_level + 1
                        : 2;
                }

                $created = CoaAccount::create([
                    'site_id' => $siteId,
                    'account_code' => $baseCode,
                    'abbreviation' => $abbr,
                    'account_name' => $account['name'],
                    'account_type' => $account['type'],
                    'short_description' => $account['description'] ?? null,
                    'parent_account_id' => $parentId,
                    'hierarchy_level' => $hierarchyLevel,
                    'is_active' => true,
                    'budget_control' => in_array($account['type'], ['EXPENSE']),
                    'category' => $account['category'] ?? 'NON_PROGRAM',
                    'sub_category' => $account['sub_category'] ?? null,
                    'typical_usage' => $account['typical_usage'] ?? null,
                    'tax_applicable' => $account['tax_applicable'] ?? false,
                    'created_by' => $createdBy,
                ]);

                $codeToId[$baseCode] = $created->id;
                $createdCount++;
            }

            return $createdCount;
        });
    }

    /**
     * Sort template accounts so parents come before children.
     *
     * @param  array<int, array<string, mixed>>  $accounts
     * @return array<int, array<string, mixed>>
     */
    private function sortTemplateAccounts(array $accounts): array
    {
        // Sort by account code to ensure parents (smaller numbers/shorter) exist before children
        // unique strings sort: "1000", "1100", "1110" -> correct order.
        usort($accounts, function ($a, $b) {
            return strcmp($a['code'], $b['code']);
        });

        return $accounts;
    }
}
