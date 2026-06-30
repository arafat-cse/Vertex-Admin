import { Permission } from './permission.model';

export interface Role {
  id: number;
  name: string;
  guardName: string;
  permissions: Permission[];
  usersCount?: number;
  createdAt: string;
}
