import { User } from './user.model';
import { ActivityLog } from './activity-log.model';

export interface DashboardStats {
  total_users: number;
  active_roles: number;
  permissions_count: number;
  today_logins: number;
  trend_users: number;
  trend_logins: number;
}

export interface ChartData {
  labels: string[];
  series: number[];
}

export type RecentUser = User;

export type ActivityFeedItem = ActivityLog;
