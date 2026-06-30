export interface Permission {
  id: number;
  name: string;
  guardName: string;
  group: string;
  createdAt: string;
}

export interface PermissionGroup {
  group: string;
  permissions: Permission[];
}
