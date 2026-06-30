# Vertex-Admin — Full Project Specification & Build Tracker

> **HOW TO RESUME**: New session → read this file → run Workflow to generate remaining files.
> Check `## BUILD PROGRESS` section to see what's done vs pending.

---

## PROJECT IDENTITY

| Key | Value |
|-----|-------|
| Project Name | Vertex-Admin |
| Sidebar Logo | "V" monogram + "Vertex-Admin" wordmark |
| Page Title | `Vertex-Admin \| [Page Name]` |
| Footer | © 2025 Vertex-Admin. All rights reserved. |
| Default Admin | admin@vertex.dev / password |
| Favicon | "V" geometric monogram |

---

## TECH STACK

### Backend
- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL 8.0+
- **Auth**: Laravel Sanctum (API token + cookie-based SPA)
- **RBAC**: Spatie Laravel Permission v6
- **Architecture**: Repository Pattern + Service Layer
- **Validation**: Form Request classes (one per action)
- **Responses**: API Resource classes (one per model)
- **Queues**: Laravel Queue (database driver)
- **Standards**: SOLID, Clean Architecture, no logic in controllers

### Frontend
- **Framework**: Angular 20 (standalone components, NO NgModules)
- **UI Library**: Angular Material 3
- **CSS**: TailwindCSS v4 + custom SCSS tokens
- **State**: Angular Signals + effect()
- **Routing**: Lazy-loaded feature routes
- **Charts**: ng-apexcharts (ApexCharts wrapper)
- **Auth**: Sanctum CSRF + Bearer token in interceptor
- **Theme**: Dark/Light toggle (persisted to localStorage)
- **Responsive**: Mobile-first, breakpoints sm/md/lg/xl
- **Font**: Inter (Google Fonts)

---

## COLOR PALETTE

```
Primary:       #7C3AED  (Violet-600)
Primary Dark:  #5B21B6  (Violet-800)
Sidebar bg:    #1E1B4B  (Indigo-950) dark mode
Light bg:      #F5F3FF  (Violet-50) light mode
Card bg light: #FFFFFF
Card bg dark:  #1F1F2E
Success:       #10B981
Warning:       #F59E0B
Danger:        #EF4444
Info:          #3B82F6
Text primary:  #111827 / #F9FAFB (dark)
Text muted:    #6B7280 / #9CA3AF (dark)
```

---

## PROJECT STRUCTURE

```
/var/www/html/Vertex-Admin/
  /backend      → Laravel 12 REST API
  /frontend     → Angular 20 SPA
  install.sh
  install.bat
  setup.ps1
  README.md
```

---

## UI/UX SPECIFICATION

### Sidebar (collapsible)
- Width: 260px open, 72px collapsed
- Logo: "V" icon + "Vertex-Admin" text (hide text when collapsed)
- Nav items: icon (24px) + label + optional badge
- Group labels: uppercase, muted, 11px (hidden collapsed)
- Active: left 3px accent bar + violet bg tint
- Hover: subtle bg highlight, icon → violet
- Bottom: collapse toggle chevron button
- Mobile: overlay drawer + semi-transparent backdrop
- Transition: 250ms ease-in-out width animation

### Top Navbar (sticky, 64px)
- Left: hamburger (mobile) + breadcrumb
- Right: search icon → modal, dark/light toggle, notification bell (badge), avatar dropdown (name, email, Profile, Settings, Logout)

### Dashboard Stat Cards (4 in a row)
- Card 1: Total Users — icon ti-users, violet bg
- Card 2: Active Roles — icon ti-shield, emerald bg
- Card 3: Permissions — icon ti-key, amber bg
- Card 4: Today's Logins — icon ti-login, sky bg
- Each: icon pill, big metric number, label, trend arrow + %

### Charts
- Area: "User registrations" — last 30 days, violet fill
- Bar: "Activity by day" — last 7 days, indigo bars
- Donut: "Users by role" — role distribution

### Data Tables
- Sticky header, sortable, row hover
- Actions: view/edit/delete icon buttons
- Bulk select checkbox column
- Pagination: prev/next + page numbers + per-page selector
- Empty state: centered illustration + message

### Status Badges
- Active: green pill | Inactive: gray pill | Pending: amber pill | Deleted: red pill

---

## BACKEND FILES TO GENERATE

### Controllers (`app/Http/Controllers/Api/`)
- [ ] AuthController.php
- [ ] UserController.php
- [ ] RoleController.php
- [ ] PermissionController.php
- [ ] DashboardController.php
- [ ] SettingsController.php
- [ ] AuditLogController.php
- [ ] ActivityLogController.php
- [ ] NotificationController.php
- [ ] ProfileController.php

### Form Requests
```
app/Http/Requests/Auth/
  - [ ] LoginRequest.php
  - [ ] ForgotPasswordRequest.php
  - [ ] ResetPasswordRequest.php
  - [ ] ChangePasswordRequest.php

app/Http/Requests/User/
  - [ ] StoreUserRequest.php
  - [ ] UpdateUserRequest.php
  - [ ] AssignRoleRequest.php

app/Http/Requests/Role/
  - [ ] StoreRoleRequest.php
  - [ ] UpdateRoleRequest.php
  - [ ] AssignPermissionsRequest.php

app/Http/Requests/Permission/
  - [ ] StorePermissionRequest.php
  - [ ] UpdatePermissionRequest.php

app/Http/Requests/Settings/
  - [ ] UpdateSettingsRequest.php
  - [ ] UpdateEmailSettingsRequest.php

app/Http/Requests/Profile/
  - [ ] UpdateProfileRequest.php
```

### API Resources (`app/Http/Resources/`)
- [ ] UserResource.php
- [ ] UserCollection.php
- [ ] RoleResource.php
- [ ] PermissionResource.php
- [ ] AuditLogResource.php
- [ ] ActivityLogResource.php
- [ ] NotificationResource.php
- [ ] SettingsResource.php
- [ ] DashboardStatsResource.php

### Middleware (`app/Http/Middleware/`)
- [ ] CheckPermission.php
- [ ] LogActivity.php
- [ ] ForceJsonResponse.php

### Models (`app/Models/`)
- [ ] User.php (soft deletes, HasRoles, HasApiTokens)
- [ ] AuditLog.php
- [ ] ActivityLog.php
- [ ] Setting.php
- [ ] Notification.php

### Repository Interfaces (`app/Repositories/Interfaces/`)
- [ ] UserRepositoryInterface.php
- [ ] RoleRepositoryInterface.php
- [ ] PermissionRepositoryInterface.php
- [ ] AuditLogRepositoryInterface.php
- [ ] ActivityLogRepositoryInterface.php
- [ ] SettingsRepositoryInterface.php
- [ ] NotificationRepositoryInterface.php

### Repository Implementations (`app/Repositories/`)
- [ ] UserRepository.php
- [ ] RoleRepository.php
- [ ] PermissionRepository.php
- [ ] AuditLogRepository.php
- [ ] ActivityLogRepository.php
- [ ] SettingsRepository.php
- [ ] NotificationRepository.php

### Services (`app/Services/`)
- [ ] AuthService.php
- [ ] UserService.php
- [ ] RoleService.php
- [ ] PermissionService.php
- [ ] DashboardService.php
- [ ] SettingsService.php
- [ ] AuditLogService.php
- [ ] ActivityLogService.php
- [ ] NotificationService.php
- [ ] ProfileService.php
- [ ] FileUploadService.php

### Support Files
```
app/Observers/
  - [ ] UserObserver.php

app/Events/
  - [ ] AuditEvent.php
  - [ ] UserCreatedEvent.php

app/Listeners/
  - [ ] LogAuditEventListener.php
  - [ ] SendWelcomeEmailListener.php

app/Jobs/
  - [ ] SendWelcomeEmailJob.php
  - [ ] ProcessAuditLogJob.php

app/Notifications/
  - [ ] WelcomeNotification.php
  - [ ] PasswordResetNotification.php
  - [ ] AdminAlertNotification.php

app/Providers/
  - [ ] AppServiceProvider.php
  - [ ] RepositoryServiceProvider.php
  - [ ] EventServiceProvider.php
  - [ ] AuthServiceProvider.php

app/Traits/
  - [ ] ApiResponseTrait.php
  - [ ] HasAuditLog.php
  - [ ] Searchable.php
```

### Database
```
database/migrations/
  - [ ] (standard Laravel migrations)
  - [ ] xxxx_create_settings_table.php
  - [ ] xxxx_create_audit_logs_table.php
  - [ ] xxxx_create_activity_logs_table.php
  - [ ] xxxx_create_notifications_table.php

database/seeders/
  - [ ] DatabaseSeeder.php
  - [ ] RolePermissionSeeder.php
  - [ ] UserSeeder.php
  - [ ] SettingsSeeder.php
```

### Config & Bootstrap
- [ ] routes/api.php
- [ ] routes/auth.php
- [ ] config/cors.php
- [ ] config/sanctum.php
- [ ] config/permission.php
- [ ] bootstrap/app.php
- [ ] composer.json
- [ ] .env.example

---

## BACKEND API ENDPOINTS

```
AUTH (public, rate-limited 5/min):
  POST   /api/auth/login
  POST   /api/auth/forgot-password
  POST   /api/auth/reset-password

AUTH (protected):
  POST   /api/auth/logout
  GET    /api/auth/me

PROFILE:
  GET    /api/profile
  PUT    /api/profile
  POST   /api/profile/avatar
  POST   /api/profile/change-password

DASHBOARD:
  GET    /api/dashboard/stats
  GET    /api/dashboard/chart/registrations
  GET    /api/dashboard/chart/activity
  GET    /api/dashboard/chart/roles-distribution
  GET    /api/dashboard/recent-users
  GET    /api/dashboard/recent-activities
  GET    /api/dashboard/latest-logins

USERS (paginated, search, filter, sort):
  GET    /api/users
  POST   /api/users
  GET    /api/users/{id}
  PUT    /api/users/{id}
  DELETE /api/users/{id}           (soft delete)
  POST   /api/users/{id}/restore
  POST   /api/users/{id}/activate
  POST   /api/users/{id}/deactivate
  POST   /api/users/{id}/assign-role
  GET    /api/users/trashed

ROLES:
  GET    /api/roles
  POST   /api/roles
  GET    /api/roles/{id}
  PUT    /api/roles/{id}
  DELETE /api/roles/{id}
  POST   /api/roles/{id}/assign-permissions
  GET    /api/roles/{id}/permissions

PERMISSIONS:
  GET    /api/permissions
  POST   /api/permissions
  GET    /api/permissions/{id}
  PUT    /api/permissions/{id}
  DELETE /api/permissions/{id}
  GET    /api/permissions/groups

SETTINGS:
  GET    /api/settings
  PUT    /api/settings/general
  PUT    /api/settings/email
  POST   /api/settings/logo
  POST   /api/settings/favicon

AUDIT LOGS:
  GET    /api/audit-logs
  GET    /api/audit-logs/{id}
  DELETE /api/audit-logs/clear     (permission: audit.clear)

ACTIVITY LOGS:
  GET    /api/activity-logs
  DELETE /api/activity-logs/clear

NOTIFICATIONS:
  GET    /api/notifications
  GET    /api/notifications/unread-count
  POST   /api/notifications/{id}/read
  POST   /api/notifications/read-all
  DELETE /api/notifications/{id}
```

---

## FRONTEND FILES TO GENERATE

### Core
```
src/app/core/
  guards/
    - [ ] auth.guard.ts
    - [ ] guest.guard.ts
    - [ ] permission.guard.ts
  interceptors/
    - [ ] auth.interceptor.ts
    - [ ] error.interceptor.ts
    - [ ] loading.interceptor.ts
  services/
    - [ ] auth.service.ts
    - [ ] user.service.ts
    - [ ] role.service.ts
    - [ ] permission.service.ts
    - [ ] dashboard.service.ts
    - [ ] settings.service.ts
    - [ ] audit-log.service.ts
    - [ ] activity-log.service.ts
    - [ ] notification.service.ts
    - [ ] profile.service.ts
    - [ ] theme.service.ts
    - [ ] storage.service.ts
    - [ ] loading.service.ts
    - [ ] toast.service.ts
  models/
    - [ ] user.model.ts
    - [ ] role.model.ts
    - [ ] permission.model.ts
    - [ ] settings.model.ts
    - [ ] audit-log.model.ts
    - [ ] activity-log.model.ts
    - [ ] notification.model.ts
    - [ ] dashboard.model.ts
    - [ ] api-response.model.ts
    - [ ] pagination.model.ts
  store/
    - [ ] auth.store.ts
    - [ ] user.store.ts
    - [ ] notification.store.ts
    - [ ] settings.store.ts
```

### Shared Components
```
src/app/shared/
  components/
    - [ ] page-header/ (ts + html + scss)
    - [ ] data-table/ (ts + html + scss)
    - [ ] confirm-dialog/ (ts + html)
    - [ ] status-badge/ (ts + html)
    - [ ] avatar/ (ts + html)
    - [ ] loading-spinner/ (ts + html)
    - [ ] empty-state/ (ts + html)
    - [ ] search-input/ (ts + html)
    - [ ] pagination/ (ts + html)
    - [ ] breadcrumb/ (ts + html)
    - [ ] stat-card/ (ts + html)
  directives/
    - [ ] has-permission.directive.ts
    - [ ] click-outside.directive.ts
    - [ ] debounce-click.directive.ts
  pipes/
    - [ ] time-ago.pipe.ts
    - [ ] truncate.pipe.ts
    - [ ] file-size.pipe.ts
```

### Layout
```
src/app/layout/
  - [ ] main-layout/ (ts + html + scss)
  - [ ] sidebar/ (component + nav-item component, ts + html + scss)
  - [ ] topbar/ (ts + html + scss)
  - [ ] notification-panel/ (ts + html + scss)
  - [ ] profile-dropdown/ (ts + html)
  - [ ] breadcrumb/ (ts + html)
```

### Features
```
src/app/features/
  auth/
    - [ ] login/ (ts + html + scss)
    - [ ] forgot-password/ (ts + html)
    - [ ] reset-password/ (ts + html)
    - [ ] auth.routes.ts

  dashboard/
    - [ ] dashboard.component.ts/html/scss
    - [ ] components/stats-row/ (ts + html)
    - [ ] components/registrations-chart/ (ts + html)
    - [ ] components/activity-chart/ (ts + html)
    - [ ] components/roles-donut-chart/ (ts + html)
    - [ ] components/recent-users/ (ts + html)
    - [ ] components/activity-feed/ (ts + html)
    - [ ] components/latest-logins/ (ts + html)
    - [ ] dashboard.routes.ts

  users/
    - [ ] users-list/ (ts + html + scss)
    - [ ] user-form/ (ts + html)
    - [ ] user-detail/ (ts + html)
    - [ ] assign-role-dialog/ (ts + html)
    - [ ] users.routes.ts

  roles/
    - [ ] roles-list/ (ts + html)
    - [ ] role-form/ (ts + html)
    - [ ] assign-permissions-dialog/ (ts + html)
    - [ ] roles.routes.ts

  permissions/
    - [ ] permissions-list/ (ts + html)
    - [ ] permission-form/ (ts + html)
    - [ ] permissions.routes.ts

  settings/
    - [ ] general-settings/ (ts + html)
    - [ ] email-settings/ (ts + html)
    - [ ] theme-settings/ (ts + html)
    - [ ] settings.routes.ts

  audit-logs/
    - [ ] audit-logs-list/ (ts + html)
    - [ ] audit-logs.routes.ts

  activity-logs/
    - [ ] activity-logs-list/ (ts + html)
    - [ ] activity-logs.routes.ts

  profile/
    - [ ] profile-form/ (ts + html)
    - [ ] change-password/ (ts + html)
    - [ ] avatar-upload/ (ts + html)
    - [ ] profile.routes.ts

  notifications/
    - [ ] notifications-list/ (ts + html)
    - [ ] notifications.routes.ts
```

### App Root & Config
```
src/app/
  - [ ] app.routes.ts
  - [ ] app.config.ts
  - [ ] app.component.ts/html/scss

styles/
  - [ ] _variables.scss
  - [ ] _theme-light.scss
  - [ ] _theme-dark.scss
  - [ ] _utilities.scss
  - [ ] styles.scss

- [ ] tailwind.config.js
- [ ] angular.json
- [ ] tsconfig.json
- [ ] tsconfig.app.json
- [ ] package.json

environment/
  - [ ] environment.ts
  - [ ] environment.prod.ts
```

---

## ANGULAR ROUTING STRUCTURE

```
/ → redirect /dashboard
/auth/login               (guest guard)
/auth/forgot-password
/auth/reset-password

/dashboard                (auth guard, lazy)
/users                    (auth guard, permission: users.view, lazy)
/users/create
/users/:id
/users/:id/edit
/roles                    (auth guard, permission: roles.view, lazy)
/roles/create
/roles/:id/edit
/permissions              (auth guard, permission: permissions.view, lazy)
/permissions/create
/permissions/:id/edit
/settings                 (auth guard, permission: settings.view, lazy)
/settings/general
/settings/email
/settings/theme
/audit-logs               (auth guard, permission: audit.view, lazy)
/activity-logs            (auth guard, lazy)
/profile                  (auth guard, lazy)
/profile/password
/notifications            (auth guard, lazy)
```

---

## SEED DATA

### Roles
| Role | Permissions |
|------|-------------|
| Super Admin | All permissions (guard: web + api) |
| Manager | users.*, roles.view, dashboard.view |
| Editor | users.view, dashboard.view |
| User | dashboard.view only |

### Permission Groups
```
dashboard  : dashboard.view
users      : users.view, users.create, users.edit, users.delete, users.restore
roles      : roles.view, roles.create, roles.edit, roles.delete
permissions: permissions.view, permissions.create, permissions.edit, permissions.delete
settings   : settings.view, settings.edit
audit      : audit.view, audit.clear
activity   : activity.view, activity.clear
```

### Default Admin User
```
Name:     Admin User
Email:    admin@vertex.dev
Password: password
Role:     Super Admin
Status:   active
```

### Default Settings
```
company_name:  Vertex-Admin
company_email: admin@vertex.dev
timezone:      Asia/Dhaka
date_format:   d M Y
theme:         light
mail_driver:   smtp
```

---

## SECURITY REQUIREMENTS

### Backend
- All API routes behind `auth:sanctum`
- Route-level permission via `CheckPermission` middleware
- Form Request validation on every write endpoint
- Rate limiting: 5 req/min on login, 60/min general
- CORS: allow only `http://localhost:4200` in dev
- Eloquent ORM only (no raw SQL)
- XSS: sanitize file names and user input
- Soft deletes on User model
- LogActivity middleware on POST/PUT/PATCH/DELETE
- Passwords: bcrypt rounds 12
- File uploads: mime-type whitelist, max 2MB, `storage/app/public`

### Frontend
- Auth guard blocks unauthenticated routes
- Permission guard per route
- HttpInterceptor adds Bearer token
- Error interceptor: 401→login, 403→forbidden, 422→validation, 500→toast
- Token in localStorage via StorageService
- `hasPermission` directive hides UI elements
- No sensitive data in route params

---

## ROOT FILES TO GENERATE

### install.sh (Linux/macOS)
- ASCII banner
- Check php >=8.2, composer, node >=18, npm
- composer install
- cp .env.example .env
- Prompt DB credentials → write to .env via sed
- php artisan key:generate && migrate --seed && storage:link
- npm install --legacy-peer-deps
- Start backend (port 8000) + frontend (ng serve) in background
- Print credentials table
- Open http://localhost:4200

### install.bat (Windows CMD)
- Same flow, @echo off, SET, findstr
- `start /b` for background processes
- `start http://localhost:4200`

### setup.ps1 (PowerShell)
- Same flow with PS syntax
- Colored output: Write-Host -ForegroundColor Cyan/Green/Red
- try/catch on each step
- Start-Process http://localhost:4200

### README.md
- Title + description
- Tech stack badges
- Prerequisites
- Quick start (installer scripts)
- Manual setup (backend + frontend)
- API docs summary
- Default credentials table
- Screenshots placeholder
- License: MIT

---

## BUILD PROGRESS

### Backend
- [ ] Setup: composer.json, .env.example, bootstrap/app.php, routes/api.php, config/
- [ ] Controllers (10 files)
- [ ] Form Requests (15 files)
- [ ] API Resources (9 files)
- [ ] Models (5 files)
- [ ] Traits (3 files) + Middleware (3 files)
- [ ] Services (11 files)
- [ ] Repository Interfaces (7 files)
- [ ] Repository Implementations (7 files)
- [ ] Observers/Events/Listeners (5 files)
- [ ] Jobs/Notifications (5 files)
- [ ] Providers (4 files)
- [ ] Migrations (7 files)
- [ ] Seeders (4 files)

### Frontend
- [ ] Models (10 TypeScript interface files)
- [ ] Guards (3) + Interceptors (3)
- [ ] Services (14 files)
- [ ] Signal Stores (4 files)
- [ ] Shared Components (11 components, ~25 files)
- [ ] Directives (3) + Pipes (3)
- [ ] Layout Components (~20 files)
- [ ] Auth Feature (3 components + routes)
- [ ] Dashboard Feature (1 + 7 sub-components + routes)
- [ ] Users Feature (4 components + routes)
- [ ] Roles Feature (3 components + routes)
- [ ] Permissions Feature (2 components + routes)
- [ ] Settings Feature (3 components + routes)
- [ ] Audit + Activity Logs (2 components + 2 routes)
- [ ] Profile Feature (3 components + routes)
- [ ] Notifications Feature (1 component + routes)
- [ ] App Root (app.routes.ts, app.config.ts, app.component.*)
- [ ] Styles (5 SCSS files)
- [ ] Config (package.json, angular.json, tsconfig, tailwind.config.js, environments)

### Root Files
- [ ] install.sh
- [ ] install.bat
- [ ] setup.ps1
- [ ] README.md

---

## HOW TO RESUME IN NEW SESSION

1. Read this file: `cat /var/www/html/Vertex-Admin/VERTEX_ADMIN_SPEC.md`
2. Check what's been generated: `find /var/www/html/Vertex-Admin -name "*.php" -o -name "*.ts" | wc -l`
3. Look at BUILD PROGRESS section — tick marks show what's done
4. Run Workflow with agents for remaining unchecked items
5. Agent prompts must include: file path, full code spec, no placeholders rule

## IMPORTANT PATTERNS

### Backend Controller Pattern
```php
<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\XxxService;
use App\Http\Requests\Xxx\StoreXxxRequest;
use App\Http\Resources\XxxResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class XxxController extends Controller
{
    use ApiResponseTrait;
    public function __construct(private readonly XxxService $xxxService) {}

    public function index(): JsonResponse {
        return $this->successResponse(XxxResource::collection($this->xxxService->getAll()));
    }
}
```

### ApiResponseTrait methods
```php
$this->successResponse($data, $message = 'Success', $code = 200)
$this->errorResponse($message, $code = 400, $errors = [])
$this->notFoundResponse($message = 'Resource not found')
$this->unauthorizedResponse($message = 'Unauthorized')
$this->forbiddenResponse($message = 'Forbidden')
$this->validationErrorResponse($errors)
$this->createdResponse($data, $message = 'Created')
$this->noContentResponse()
```

### Frontend Component Pattern
```typescript
import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatXxxModule } from '@angular/material/xxx';

@Component({
  selector: 'app-xxx',
  standalone: true,
  imports: [CommonModule, MatXxxModule],
  templateUrl: './xxx.component.html',
  styleUrls: ['./xxx.component.scss']
})
export class XxxComponent implements OnInit {
  private xxxService = inject(XxxService);
  items = signal<Xxx[]>([]);
  loading = signal(false);

  ngOnInit(): void { this.loadItems(); }

  private loadItems(): void {
    this.loading.set(true);
    this.xxxService.getAll().subscribe({
      next: (res) => { this.items.set(res.data); this.loading.set(false); },
      error: () => { this.loading.set(false); }
    });
  }
}
```

### Frontend Service Pattern
```typescript
import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { ApiResponse, PaginatedResponse } from '../models/api-response.model';
import { Xxx } from '../models/xxx.model';

@Injectable({ providedIn: 'root' })
export class XxxService {
  private http = inject(HttpClient);
  private apiUrl = `${environment.apiUrl}/xxxs`;

  getAll(): Observable<ApiResponse<Xxx[]>> {
    return this.http.get<ApiResponse<Xxx[]>>(this.apiUrl);
  }
}
```

### API Response Format
```json
{
  "success": true,
  "message": "Success",
  "data": { ... },
  "errors": null
}
```

### Paginated Response Format
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "data": [ ... ],
    "meta": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 15,
      "total": 73
    },
    "links": { "first": "...", "last": "...", "prev": null, "next": "..." }
  }
}
```
