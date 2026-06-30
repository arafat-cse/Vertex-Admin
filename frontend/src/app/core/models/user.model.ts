export interface User {
  id: number;
  name: string;
  email: string;
  status: 'active' | 'inactive' | 'pending';
  avatar: string | null;
  avatarUrl: string | null;
  lastLoginAt: string | null;
  emailVerifiedAt: string | null;
  roles: string[];
  permissions: string[];
  createdAt: string;
  updatedAt: string;
}
