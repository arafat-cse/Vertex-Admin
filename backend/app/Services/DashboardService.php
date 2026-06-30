<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardService
{
    /**
     * Create a new DashboardService instance.
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Return top-level summary statistics for the dashboard.
     *
     * @return array{total_users: int, active_roles: int, permissions: int, today_logins: int}
     */
    public function getStats(): array
    {
        return [
            'total_users'  => User::count(),
            'active_roles' => Role::count(),
            'permissions'  => Permission::count(),
            'today_logins' => User::whereDate('last_login_at', Carbon::today())->count(),
        ];
    }

    /**
     * Return daily user registration counts for the last 30 days.
     *
     * @return array<int, array{date: string, count: int}>
     */
    public function getRegistrationsChart(): array
    {
        $start = Carbon::now()->subDays(29)->startOfDay();

        $rows = User::withTrashed()
            ->where('created_at', '>=', $start)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Build a complete 30-day range, filling zeros for missing dates.
        $result = [];
        for ($i = 29; $i >= 0; $i--) {
            $date   = Carbon::now()->subDays($i)->toDateString();
            $result[] = [
                'date'  => $date,
                'count' => isset($rows[$date]) ? (int) $rows[$date]->count : 0,
            ];
        }

        return $result;
    }

    /**
     * Return daily activity log counts for the last 7 days.
     *
     * @return array<int, array{date: string, count: int}>
     */
    public function getActivityChart(): array
    {
        $start = Carbon::now()->subDays(6)->startOfDay();

        $rows = ActivityLog::where('created_at', '>=', $start)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date     = Carbon::now()->subDays($i)->toDateString();
            $result[] = [
                'date'  => $date,
                'count' => isset($rows[$date]) ? (int) $rows[$date]->count : 0,
            ];
        }

        return $result;
    }

    /**
     * Return all roles with their assigned user count.
     *
     * @return array<int, array{id: int, name: string, users_count: int}>
     */
    public function getRolesDistribution(): array
    {
        return Role::withCount('users')
            ->orderByDesc('users_count')
            ->get()
            ->map(fn (Role $role) => [
                'id'          => $role->id,
                'name'        => $role->name,
                'users_count' => $role->users_count,
            ])
            ->toArray();
    }

    /**
     * Return the 5 most recently registered users.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getRecentUsers(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = User::with('roles')
            ->latest()
            ->limit(5)
            ->get();

        return UserResource::collection($users);
    }

    /**
     * Return the 10 most recent activity log entries, with their user relation.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, ActivityLog>
     */
    public function getRecentActivities(): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();
    }

    /**
     * Return the 10 users who logged in most recently.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getLatestLogins(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = User::with('roles')
            ->whereNotNull('last_login_at')
            ->orderByDesc('last_login_at')
            ->limit(10)
            ->get();

        return UserResource::collection($users);
    }
}
