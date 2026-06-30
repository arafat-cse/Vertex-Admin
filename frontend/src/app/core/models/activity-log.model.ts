export interface ActivityLog {
  id: number;
  method: string;
  url: string;
  ip_address: string;
  status_code: number;
  payload: any;
  user: {
    id: number;
    name: string;
    email: string;
  } | null;
  createdAt: string;
}
