<?php

namespace App\Data;

use App\Models\User;

class UserData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $full_name,
        public bool $is_active,
        public bool $must_change_password,
        public ?int $primary_site_id,
        public ?string $primary_site_name,
        public array $sites,
        public array $roles,
        public ?string $last_login_at,
        public ?string $last_login_ip,
        public ?string $email_verified_at,
        public string $created_at,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            full_name: $user->full_name,
            is_active: $user->is_active,
            must_change_password: $user->must_change_password,
            primary_site_id: $user->primary_site_id,
            primary_site_name: $user->primarySite?->site_name,
            sites: $user->sites
                ->map(
                    fn ($site) => [
                        'id' => $site->id,
                        'name' => $site->site_name,
                        'code' => $site->site_code,
                        'granted_at' => $site->pivot->granted_at?->toISOString(),
                        'granted_by' => $site->pivot->granted_by,
                    ],
                )
                ->toArray(),
            roles: $user->roles
                ->map(
                    fn ($role) => [
                        'id' => $role->id,
                        'name' => $role->name,
                    ],
                )
                ->toArray(),
            last_login_at: $user->last_login_at?->toISOString(),
            last_login_ip: $user->last_login_ip,
            email_verified_at: $user->email_verified_at?->toISOString(),
            created_at: $user->created_at->toISOString(),
        );
    }
}
