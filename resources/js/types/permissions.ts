export type PermissionTranslation = {
    label: string;
    description: string;
};

export type PermissionTranslations = {
    [permissionKey: string]: PermissionTranslation;
};

export type PermissionWithTranslation = {
    permission: string;
    label: string;
    description: string;
};

export type GroupedPermissions = {
    [actionType: string]: PermissionWithTranslation[];
};
