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
            $conflicts[$account['code']] = CoaAccount::where('site_id', $siteId)
                ->where('account_code', $account['code'])
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
    public function applyTemplate(string $templateKey, int $siteId, bool $skipExisting = false): int
    {
        $templates = $this->getTemplates();
        $template = $templates[$templateKey] ?? null;

        if (! $template) {
            throw new \InvalidArgumentException("Template '{$templateKey}' not found.");
        }

        $conflicts = $this->checkConflicts($templateKey, $siteId);
        $hasConflicts = in_array(true, $conflicts, true);

        if ($hasConflicts && ! $skipExisting) {
            throw new \InvalidArgumentException('Template has conflicting accounts. Use skipExisting to skip them.');
        }

        return DB::transaction(function () use ($template, $siteId, $conflicts, $skipExisting) {
            $createdCount = 0;
            $codeToId = [];

            // Sort: accounts without parents first, then those with parents
            $sorted = $this->sortTemplateAccounts($template['accounts']);

            foreach ($sorted as $account) {
                // Skip if account already exists and skipExisting is true
                if ($skipExisting && ($conflicts[$account['code']] ?? false)) {
                    // Still map the existing account's ID for child resolution
                    $existing = CoaAccount::where('site_id', $siteId)
                        ->where('account_code', $account['code'])->first();
                    if ($existing) {
                        $codeToId[$account['code']] = $existing->id;
                    }

                    continue;
                }

                $parentId = null;
                $hierarchyLevel = 1;

                if ($account['parent'] && isset($codeToId[$account['parent']])) {
                    $parentId = $codeToId[$account['parent']];
                    $parent = CoaAccount::find($parentId);
                    $hierarchyLevel = $parent ? $parent->hierarchy_level + 1 : 2;
                }

                $created = CoaAccount::create([
                    'site_id' => $siteId,
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'account_type' => $account['type'],
                    'short_description' => $account['description'] ?? null,
                    'parent_account_id' => $parentId,
                    'hierarchy_level' => $hierarchyLevel,
                    'is_active' => true,
                ]);

                $codeToId[$account['code']] = $created->id;
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
        $withoutParent = [];
        $withParent = [];

        foreach ($accounts as $account) {
            if ($account['parent'] === null) {
                $withoutParent[] = $account;
            } else {
                $withParent[] = $account;
            }
        }

        return array_merge($withoutParent, $withParent);
    }
}
