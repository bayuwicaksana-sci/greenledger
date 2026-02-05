<?php

namespace App\Models;

use App\Models\Traits\RequiresApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoaAccount extends Model
{
    use HasFactory, RequiresApproval;

    public const APPROVAL_DISPLAY_NAME = 'COA Account';

    // Account type constants
    public const TYPE_ASSET = 'ASSET';

    public const TYPE_LIABILITY = 'LIABILITY';

    public const TYPE_EQUITY = 'EQUITY';

    public const TYPE_REVENUE = 'REVENUE';

    public const TYPE_EXPENSE = 'EXPENSE';

    protected $fillable = [
        'site_id',
        'account_code',
        'abbreviation',
        'account_name',
        'short_description',
        'account_type',
        'parent_account_id',
        'hierarchy_level',
        'is_active',
        'initial_budget',
        'budget_control',
        'category',
        'sub_category',
        'typical_usage',
        'tax_applicable',
        'first_transaction_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the display attribute.
     */
    protected $appends = ['display_code'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'budget_control' => 'boolean',
            'tax_applicable' => 'boolean',
            'first_transaction_at' => 'datetime',
        ];
    }

    /**
     * Called after the approval workflow transitions to Approved.
     */
    public function onApproved(): void
    {
        $this->is_active = true;
        $this->save();
    }

    /**
     * Get the formatted account code.
     * Now just returns the account_code as the logic is simplified.
     */
    public function getDisplayCodeAttribute(): string
    {
        return (string) $this->account_code;
    }

    /**
     * Get the site that owns the COA account.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the parent account (for hierarchy).
     */
    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'parent_account_id');
    }

    /**
     * Get the child accounts (for hierarchy).
     */
    public function childAccounts(): HasMany
    {
        return $this->hasMany(CoaAccount::class, 'parent_account_id');
    }

    /**
     * Get the budget allocations for this account.
     */
    public function budgetAllocations(): HasMany
    {
        return $this->hasMany(CoaBudgetAllocation::class);
    }

    /**
     * Get the payment splits using this COA account.
     */
    public function paymentSplits(): HasMany
    {
        return $this->hasMany(PaymentRequestSplit::class);
    }

    /**
     * Get the revenue harvests using this COA account.
     */
    public function revenueHarvests(): HasMany
    {
        return $this->hasMany(RevenueHarvest::class);
    }

    /**
     * Get the revenue testing services using this COA account.
     */
    public function revenueTestingServices(): HasMany
    {
        return $this->hasMany(RevenueTestingService::class);
    }

    /**
     * Get the user who created the account.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the account.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if the account has any transactions.
     */
    public function hasTransactions(): bool
    {
        return $this->paymentSplits()->exists() ||
            $this->revenueHarvests()->exists() ||
            $this->revenueTestingServices()->exists();
    }

    /**
     * Get the total count of transactions for this account.
     */
    public function getTransactionCount(): int
    {
        return $this->paymentSplits()->count() +
            $this->revenueHarvests()->count() +
            $this->revenueTestingServices()->count();
    }

    /**
     * Check if the account code and type are locked (has transactions).
     */
    public function isLocked(): bool
    {
        return $this->first_transaction_at !== null;
    }

    /**
     * Get the balance (Actual - Budget).
     *
     * When allocated_budget is available (index page), uses it.
     * Otherwise falls back to initial_budget (edit form).
     */
    public function getBalanceAttribute(): float
    {
        $budget = array_key_exists('allocated_budget', $this->attributes)
            ? (float) $this->allocated_budget
            : (float) $this->initial_budget;

        return (float) ($this->actual_amount ?? 0) - $budget;
    }
}
