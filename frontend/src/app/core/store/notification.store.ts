import { Injectable, signal, computed } from '@angular/core';

export interface Notification {
  id: string;
  title: string;
  message: string;
  type: 'info' | 'success' | 'warning' | 'error';
  read: boolean;
  createdAt: string;
  link?: string;
}

@Injectable({ providedIn: 'root' })
export class NotificationStore {
  private _notifications = signal<Notification[]>([]);
  private _unreadCount = signal<number>(0);

  readonly notifications = this._notifications.asReadonly();
  readonly unreadCount = this._unreadCount.asReadonly();
  readonly hasUnread = computed(() => this._unreadCount() > 0);
  readonly readNotifications = computed(() =>
    this._notifications().filter(n => n.read)
  );
  readonly unreadNotifications = computed(() =>
    this._notifications().filter(n => !n.read)
  );

  setNotifications(notifications: Notification[]): void {
    this._notifications.set(notifications);
    this._unreadCount.set(notifications.filter(n => !n.read).length);
  }

  addNotification(notification: Notification): void {
    this._notifications.update(notifications => [notification, ...notifications]);
    if (!notification.read) {
      this._unreadCount.update(count => count + 1);
    }
  }

  markAsRead(id: string): void {
    this._notifications.update(notifications =>
      notifications.map(n => {
        if (n.id === id && !n.read) {
          this._unreadCount.update(count => Math.max(0, count - 1));
          return { ...n, read: true };
        }
        return n;
      })
    );
  }

  markAllAsRead(): void {
    this._notifications.update(notifications =>
      notifications.map(n => ({ ...n, read: true }))
    );
    this._unreadCount.set(0);
  }

  removeNotification(id: string): void {
    const notification = this._notifications().find(n => n.id === id);
    if (notification && !notification.read) {
      this._unreadCount.update(count => Math.max(0, count - 1));
    }
    this._notifications.update(notifications =>
      notifications.filter(n => n.id !== id)
    );
  }

  setUnreadCount(count: number): void {
    this._unreadCount.set(count);
  }

  clearAll(): void {
    this._notifications.set([]);
    this._unreadCount.set(0);
  }
}
