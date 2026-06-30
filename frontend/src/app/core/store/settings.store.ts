import { Injectable, signal, computed } from '@angular/core';

export interface Settings {
  company_name: string;
  company_email: string;
  timezone: string;
  date_format: string;
  theme: 'light' | 'dark';
  mail_driver: string;
  [key: string]: string;
}

@Injectable({ providedIn: 'root' })
export class SettingsStore {
  private _settings = signal<Settings | null>(null);

  readonly settings = this._settings.asReadonly();
  readonly isLoaded = computed(() => this._settings() !== null);
  readonly theme = computed(() => this._settings()?.theme ?? 'light');
  readonly timezone = computed(() => this._settings()?.timezone ?? 'UTC');
  readonly dateFormat = computed(() => this._settings()?.date_format ?? 'd M Y');
  readonly companyName = computed(() => this._settings()?.company_name ?? 'Vertex-Admin');
  readonly companyEmail = computed(() => this._settings()?.company_email ?? '');

  setSettings(settings: Settings): void {
    this._settings.set(settings);
  }

  updateSetting(key: string, value: string): void {
    const current = this._settings();
    if (current === null) {
      return;
    }
    this._settings.set({ ...current, [key]: value });
  }

  patchSettings(partial: Partial<Settings>): void {
    const current = this._settings();
    if (current === null) {
      return;
    }
    this._settings.set({ ...current, ...partial });
  }

  reset(): void {
    this._settings.set(null);
  }
}
