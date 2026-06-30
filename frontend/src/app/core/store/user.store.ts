import { Injectable, signal, computed } from '@angular/core';
import { User } from '../models/user.model';
import { PaginationMeta } from '../models/api-response.model';

@Injectable({ providedIn: 'root' })
export class UserStore {
  private _users = signal<User[]>([]);
  private _loading = signal<boolean>(false);
  private _error = signal<string | null>(null);
  private _pagination = signal<PaginationMeta | null>(null);

  readonly users = this._users.asReadonly();
  readonly loading = this._loading.asReadonly();
  readonly error = this._error.asReadonly();
  readonly pagination = this._pagination.asReadonly();

  readonly totalUsers = computed(() => this._pagination()?.total ?? this._users().length);
  readonly hasUsers = computed(() => this._users().length > 0);
  readonly currentPage = computed(() => this._pagination()?.current_page ?? 1);
  readonly lastPage = computed(() => this._pagination()?.last_page ?? 1);
  readonly hasNextPage = computed(() => {
    const meta = this._pagination();
    return meta ? meta.current_page < meta.last_page : false;
  });
  readonly hasPrevPage = computed(() => {
    const meta = this._pagination();
    return meta ? meta.current_page > 1 : false;
  });

  setUsers(users: User[]): void {
    this._users.set(users);
  }

  setLoading(loading: boolean): void {
    this._loading.set(loading);
  }

  setError(error: string | null): void {
    this._error.set(error);
  }

  setPagination(pagination: PaginationMeta | null): void {
    this._pagination.set(pagination);
  }

  addUser(user: User): void {
    this._users.update(users => [user, ...users]);
  }

  updateUser(updatedUser: User): void {
    this._users.update(users =>
      users.map(u => (u.id === updatedUser.id ? updatedUser : u))
    );
  }

  removeUser(id: number): void {
    this._users.update(users => users.filter(u => u.id !== id));
  }

  reset(): void {
    this._users.set([]);
    this._loading.set(false);
    this._error.set(null);
    this._pagination.set(null);
  }
}
