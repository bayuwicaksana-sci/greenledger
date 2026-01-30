<?php

namespace App\Data;

use App\Models\Site;
use App\Models\UserInvitation;
use Spatie\Permission\Models\Role;

class UserInvitationData
{
    public function __construct(
        public int $id,
        public string $email,
        public string $full_name,
        public string $primary_site_name,
        public array $additional_sites,
        public array $roles,
        public string $invited_by_name,
        public string $expires_at,
        public bool $is_expired,
        public ?string $accepted_at,
        public string $created_at,
    ) {}

    public static function fromModel(UserInvitation $invitation): self
    {
        // Get additional site names
        $additionalSites = [];
        if ($invitation->additional_site_ids) {
            $additionalSites = Site::whereIn('id', $invitation->additional_site_ids)
                ->pluck('site_name')
                ->toArray();
        }

        // Get role names
        $roles = [];
        if ($invitation->role_ids) {
            $roles = Role::whereIn('id', $invitation->role_ids)
                ->pluck('name')
                ->toArray();
        }

        return new self(
            id: $invitation->id,
            email: $invitation->email,
            full_name: $invitation->full_name ?? '',
            primary_site_name: $invitation->primarySite->site_name,
            additional_sites: $additionalSites,
            roles: $roles,
            invited_by_name: $invitation->invitedBy->name,
            expires_at: $invitation->expires_at->toISOString(),
            is_expired: $invitation->isExpired(),
            accepted_at: $invitation->accepted_at?->toISOString(),
            created_at: $invitation->created_at->toISOString(),
        );
    }
}
