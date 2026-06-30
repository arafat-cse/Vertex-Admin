import { Injectable, signal, computed, inject } from '@angular/core';
import { User } from '../models/user.model';
import { StorageService } from '../services/storage.service';

@Injectable({ providedIn: 'root' })
export class AuthStore {
  private storage = inject(StorageService);

  private _user = signal<User | null>(null);
  private _token = signal<string | null>(this.storage.getToken());

  readonly user = this._user.asReadonly();
  readonly token = this._token.asReadonly();
  readonly isAuthenticated = computed(() => !!this._token());
  readonly permissions = computed(() => this._user()?.permissions ?? []);
  readonly roles = computed(() => this._user()?.roles ?? []);

  setUser(user: User): void {
    this._user.set(user);
  }

  setToken(token: string): void {
    this._token.set(token);
    this.storage.setToken(token);
  }

  clear(): void {
    this._user.set(null);
    this._token.set(null);
    this.storage.removeToken();
  }

  hasPermission(perm: string): boolean {
    return this.permissions().includes(perm);
  }

  hasRole(role: string): boolean {
    return this.roles().includes(role);
  }
}
