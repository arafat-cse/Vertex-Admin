export interface AuditLog {
  id: number;
  event: string;
  auditable_type: string;
  auditable_id: number;
  old_values: any;
  new_values: any;
  ip_address: string;
  user_agent: string;
  user: {
    id: number;
    name: string;
    email: string;
  } | null;
  createdAt: string;
}
