export interface Notification {
  id: string;
  title: string;
  message: string;
  type: 'info' | 'success' | 'warning' | 'danger';
  is_read: boolean;
  read_at: string | null;
  createdAt: string;
}
