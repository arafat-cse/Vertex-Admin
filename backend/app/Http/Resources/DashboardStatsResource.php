<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Wraps dashboard statistics for the API response.
 *
 * Pass an associative array (or any object castable to array) produced by
 * DashboardService::getStats(), or supply the four expected keys directly:
 *   total_users, active_roles, permissions_count, today_logins.
 *
 * Usage:
 *   return new DashboardStatsResource($dashboardService->getStats());
 *
 * When the resource is constructed with an empty / null value the counts are
 * queried fresh from the database so the resource can also be used stand-alone.
 */
class DashboardStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? $this->resource : (array) $this->resource;

        // ---------------------------------------------------------------------------
        // Core counts — use values from the injected data when available, otherwise
        // fall back to a live DB query so the resource is usable stand-alone.
        // ---------------------------------------------------------------------------
        $totalUsers       = (int) ($data['total_users']       ?? User::count());
        $activeRoles      = (int) ($data['active_roles']      ?? Role::count());
        $permissionsCount = (int) ($data['permissions_count'] ?? $data['permissions'] ?? Permission::count());
        $todayLogins      = (int) ($data['today_logins']      ?? User::whereDate('last_login_at', Carbon::today())->count());

        // ---------------------------------------------------------------------------
        // Trend: percentage change of today's logins versus yesterday's logins.
        // A positive value means more logins today; negative means fewer.
        // When yesterday had 0 logins we return null (division by zero guard).
        // ---------------------------------------------------------------------------
        $yesterdayLogins = (int) ($data['yesterday_logins'] ?? User::whereDate('last_login_at', Carbon::yesterday())->count());

        $loginTrendPercent = null;

        if ($yesterdayLogins > 0) {
            $loginTrendPercent = round(
                (($todayLogins - $yesterdayLogins) / $yesterdayLogins) * 100,
                2
            );
        } elseif ($todayLogins > 0) {
            // Yesterday had 0 logins but today has some — treat as +100 %
            $loginTrendPercent = 100.0;
        }

        return [
            'total_users'       => $totalUsers,
            'active_roles'      => $activeRoles,
            'permissions_count' => $permissionsCount,
            'today_logins'      => [
                'count'            => $todayLogins,
                'yesterday_count'  => $yesterdayLogins,
                'trend_percentage' => $loginTrendPercent,
            ],
        ];
    }
}
