<?php

namespace App\Services;

use App\Models\CoaAccount;
use App\Models\Site;
use Illuminate\Support\Facades\DB;

class CoaAccountImportService
{
    /**
     * Validate imported rows and return validation errors per row.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, string>>
     */
    public function validate(array $rows): array
    {
        $errors = [];
        $siteCache = [];
        $seenCodes = []; // Track codes within the same batch per site

        foreach ($rows as $index => $row) {
            $rowErrors = [];

            // Resolve and cache site
            $siteCode = $row['site_code'] ?? '';
            if (! isset($siteCache[$siteCode])) {
                $siteCache[$siteCode] = Site::where('site_code', $siteCode)->first();
            }
            $site = $siteCache[$siteCode];

            if (! $site) {
                $rowErrors['site_code'] = "Site '{$siteCode}' not found.";
            } else {
                // Check for duplicate account_code within the batch for the same site
                $accountCode = $row['account_code'] ?? '';
                $batchKey = "{$site->id}:{$accountCode}";

                if (isset($seenCodes[$batchKey])) {
                    $rowErrors['account_code'] = "Duplicate account code '{$accountCode}' in import (also at row ".($seenCodes[$batchKey] + 1).').';
                } else {
                    $seenCodes[$batchKey] = $index;
                }

                // Check if account code already exists in the database
                if (CoaAccount::where('site_id', $site->id)
                    ->where('account_code', $accountCode)->exists()
                ) {
                    $rowErrors['account_code'] = "Account code '{$accountCode}' already exists for site '{$siteCode}'.";
                }

                // Validate parent_account_code reference
                $parentCode = $row['parent_account_code'] ?? null;
                if ($parentCode) {
                    $parentExistsInDb = CoaAccount::where('site_id', $site->id)
                        ->where('account_code', $parentCode)->exists();
                    $parentExistsInBatch = collect($rows)->contains(function ($r) use ($parentCode, $siteCode) {
                        return ($r['site_code'] ?? '') === $siteCode
                            && ($r['account_code'] ?? '') === $parentCode;
                    });

                    if (! $parentExistsInDb && ! $parentExistsInBatch) {
                        $rowErrors['parent_account_code'] = "Parent account code '{$parentCode}' not found for site '{$siteCode}'.";
                    }
                }
            }

            if (! empty($rowErrors)) {
                $errors[$index] = $rowErrors;
            }
        }

        return $errors;
    }

    /**
     * Import validated rows into the database.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return int Number of accounts created.
     */
    public function import(array $rows): int
    {
        return DB::transaction(function () use ($rows) {
            $siteCache = [];
            $createdCount = 0;

            // First pass: create accounts without parents (or with existing parents)
            // Second pass: create accounts whose parents were just created
            $pending = [];

            foreach ($rows as $row) {
                $site = $this->resolveSite($row['site_code'], $siteCache);
                $parentCode = $row['parent_account_code'] ?? null;

                if ($parentCode) {
                    $parent = CoaAccount::where('site_id', $site->id)
                        ->where('account_code', $parentCode)->first();

                    if ($parent) {
                        $this->createAccount($row, $site->id, $parent);
                        $createdCount++;
                    } else {
                        $pending[] = $row;
                    }
                } else {
                    $this->createAccount($row, $site->id);
                    $createdCount++;
                }
            }

            // Resolve pending accounts (parents created in this batch)
            foreach ($pending as $row) {
                $site = $this->resolveSite($row['site_code'], $siteCache);
                $parent = CoaAccount::where('site_id', $site->id)
                    ->where('account_code', $row['parent_account_code'])->first();

                $this->createAccount($row, $site->id, $parent);
                $createdCount++;
            }

            return $createdCount;
        });
    }

    private function resolveSite(string $siteCode, array &$cache): Site
    {
        if (! isset($cache[$siteCode])) {
            $cache[$siteCode] = Site::where('site_code', $siteCode)->firstOrFail();
        }

        return $cache[$siteCode];
    }

    private function createAccount(array $row, int $siteId, ?CoaAccount $parent = null): CoaAccount
    {
        return CoaAccount::create([
            'site_id' => $siteId,
            'account_code' => $row['account_code'],
            'account_name' => $row['account_name'],
            'account_type' => $row['account_type'],
            'short_description' => $row['short_description'] ?? null,
            'parent_account_id' => $parent?->id,
            'hierarchy_level' => $parent ? $parent->hierarchy_level + 1 : 1,
            'is_active' => $row['is_active'],
        ]);
    }
}
