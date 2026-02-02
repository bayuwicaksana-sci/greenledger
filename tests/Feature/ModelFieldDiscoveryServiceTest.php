<?php

use App\Models\PaymentRequest;
use App\Models\Program;
use App\Models\Settlement;
use App\Services\Approval\ModelFieldDiscoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

describe('ModelFieldDiscoveryService', function () {
    beforeEach(function () {
        $this->service = new ModelFieldDiscoveryService;
    });

    it('discovers fields from PaymentRequest model', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);

        expect($result)->toHaveKey('fields')
            ->and($result['fields'])->toBeArray()
            ->and($result['fields'])->not->toBeEmpty();

        // Check for expected PaymentRequest fields
        $fieldValues = collect($result['fields'])->pluck('value')->toArray();

        expect($fieldValues)->toContain('total_amount')
            ->and($fieldValues)->toContain('vendor_name')
            ->and($fieldValues)->toContain('status')
            ->and($fieldValues)->toContain('request_date');
    });

    it('correctly detects field types', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);
        $fields = collect($result['fields']);

        // Find specific fields and check their types
        $totalAmount = $fields->firstWhere('value', 'total_amount');
        $vendorName = $fields->firstWhere('value', 'vendor_name');
        $isMultiProgram = $fields->firstWhere('value', 'is_multi_program');
        $requestDate = $fields->firstWhere('value', 'request_date');

        expect($totalAmount)->not->toBeNull()
            ->and($totalAmount['type'])->toBe('number');

        expect($vendorName)->not->toBeNull()
            ->and($vendorName['type'])->toBe('string');

        expect($isMultiProgram)->not->toBeNull()
            ->and($isMultiProgram['type'])->toBe('boolean');

        expect($requestDate)->not->toBeNull()
            ->and($requestDate['type'])->toBe('date');
    });

    it('generates human-readable labels', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);
        $fields = collect($result['fields']);

        $totalAmount = $fields->firstWhere('value', 'total_amount');
        $vendorName = $fields->firstWhere('value', 'vendor_name');

        expect($totalAmount)->not->toBeNull()
            ->and($totalAmount['label'])->toBe('Total Amount');

        expect($vendorName)->not->toBeNull()
            ->and($vendorName['label'])->toBe('Vendor Name');
    });

    it('discovers BelongsTo relationship fields', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);
        $fields = collect($result['fields']);

        // PaymentRequest belongs to Site
        $siteName = $fields->firstWhere('value', 'site.site_name');

        expect($siteName)->not->toBeNull()
            ->and($siteName['label'])->toBe('Site Site Name')
            ->and($siteName['type'])->toBe('string')
            ->and($siteName['relationship'])->toBeTrue();
    });

    it('excludes internal Laravel columns', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);
        $fieldValues = collect($result['fields'])->pluck('value')->toArray();

        expect($fieldValues)->not->toContain('id')
            ->and($fieldValues)->not->toContain('created_at')
            ->and($fieldValues)->not->toContain('updated_at')
            ->and($fieldValues)->not->toContain('deleted_at');
    });

    it('excludes foreign key columns', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);
        $fieldValues = collect($result['fields'])->pluck('value')->toArray();

        // Foreign keys should be excluded (represented via relationships instead)
        expect($fieldValues)->not->toContain('site_id')
            ->and($fieldValues)->not->toContain('created_by')
            ->and($fieldValues)->not->toContain('approved_by');
    });

    it('caches discovery results', function () {
        Cache::flush();

        // First call should cache the result
        $result1 = $this->service->discoverFields(PaymentRequest::class);

        // Verify it's cached
        $cacheKey = 'approval.model_fields.App_Models_PaymentRequest';
        expect(Cache::has($cacheKey))->toBeTrue();

        // Second call should return cached result
        $result2 = $this->service->discoverFields(PaymentRequest::class);

        expect($result1)->toEqual($result2);
    });

    it('handles non-existent model class gracefully', function () {
        $result = $this->service->discoverFields('App\\Models\\NonExistentModel');

        expect($result)->toHaveKey('fields')
            ->and($result['fields'])->toBeArray()
            ->and($result['fields'])->toBeEmpty();
    });

    it('discovers fields from Program model', function () {
        $result = $this->service->discoverFields(Program::class);
        $fieldValues = collect($result['fields'])->pluck('value')->toArray();

        expect($fieldValues)->toContain('program_name')
            ->and($fieldValues)->toContain('program_code')
            ->and($fieldValues)->toContain('total_budget')
            ->and($fieldValues)->toContain('fiscal_year')
            ->and($fieldValues)->toContain('status');
    });

    it('discovers fields from Settlement model', function () {
        $result = $this->service->discoverFields(Settlement::class);
        $fieldValues = collect($result['fields'])->pluck('value')->toArray();

        expect($fieldValues)->toContain('actual_amount')
            ->and($fieldValues)->toContain('surplus_amount')
            ->and($fieldValues)->toContain('status');
    });

    it('clears cache for specific model', function () {
        Cache::flush();

        // Cache some results
        $this->service->discoverFields(PaymentRequest::class);
        $this->service->discoverFields(Program::class);

        $paymentCacheKey = 'approval.model_fields.App_Models_PaymentRequest';
        $programCacheKey = 'approval.model_fields.App_Models_Program';

        expect(Cache::has($paymentCacheKey))->toBeTrue()
            ->and(Cache::has($programCacheKey))->toBeTrue();

        // Clear only PaymentRequest cache
        $this->service->clearCache(PaymentRequest::class);

        expect(Cache::has($paymentCacheKey))->toBeFalse()
            ->and(Cache::has($programCacheKey))->toBeTrue();
    });

    it('sets nullable flag correctly', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);
        $fields = collect($result['fields']);

        // Check fields have nullable property
        foreach ($fields as $field) {
            expect($field)->toHaveKey('nullable')
                ->and($field['nullable'])->toBeIn([true, false]);
        }
    });

    it('sorts fields alphabetically by label', function () {
        $result = $this->service->discoverFields(PaymentRequest::class);
        $labels = collect($result['fields'])->pluck('label')->toArray();

        $sortedLabels = $labels;
        sort($sortedLabels);

        expect($labels)->toEqual($sortedLabels);
    });
});
