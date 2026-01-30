export type Role = {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
    permissions_count?: number;
    users_count?: number;
    permissions?: Permission[];
};

export type Permission = {
    id: number;
    name: string;
    guard_name: string;
};

export type RoleFormData = {
    name: string;
    permissions?: string[];
};

export type RoleWithPermissions = Role & {
    permissions: Permission[];
};

export type PermissionWithTranslation = {
    name: string;
    label: string;
    description: string;
    selected: boolean;
    action: string;
};

export type PermissionGroup = {
    action: string;
    count: number;
    selected: number;
    permissions: PermissionWithTranslation[];
};
