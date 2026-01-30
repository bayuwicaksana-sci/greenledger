<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Parental\HasChildren;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasChildren,
        HasFactory,
        HasRoles,
        Notifiable,
        SoftDeletes,
        TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'full_name',
        'primary_site_id',
        'is_active',
        'must_change_password',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Get the primary site for the user.
     */
    public function primarySite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'primary_site_id');
    }

    /**
     * Get the sites that the user has access to.
     */
    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class)
            ->using(SiteUser::class)
            ->withPivot('granted_at', 'granted_by')
            ->withTimestamps();
    }

    /**
     * Get the subsidi claims for the user.
     */
    public function subsidiClaims(): HasMany
    {
        return $this->hasMany(SubsidiClaim::class);
    }

    /**
     * Get the programs created by the user.
     */
    public function createdPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'created_by');
    }

    /**
     * Get the payment requests created by the user.
     */
    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class, 'created_by');
    }

    /**
     * Get the digital signatures for the user.
     */
    public function digitalSignatures(): HasMany
    {
        return $this->hasMany(DigitalSignature::class);
    }
}
