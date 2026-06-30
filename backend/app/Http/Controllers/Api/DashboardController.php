<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * Return top-level summary statistics.
     *
     * GET /api/dashboard/stats
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getStats();

            return $this->successResponse($stats, 'Dashboard statistics retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve dashboard statistics.', 500, $e->getMessage());
        }
    }

    /**
     * Return daily user registration counts for the last 30 days.
     *
     * GET /api/dashboard/registrations-chart
     */
    public function registrationsChart(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getRegistrationsChart();

            return $this->successResponse($data, 'Registrations chart data retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve registrations chart data.', 500, $e->getMessage());
        }
    }

    /**
     * Return daily activity log counts for the last 7 days.
     *
     * GET /api/dashboard/activity-chart
     */
    public function activityChart(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getActivityChart();

            return $this->successResponse($data, 'Activity chart data retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve activity chart data.', 500, $e->getMessage());
        }
    }

    /**
     * Return all roles with their assigned user count.
     *
     * GET /api/dashboard/roles-distribution
     */
    public function rolesDistribution(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getRolesDistribution();

            return $this->successResponse($data, 'Roles distribution retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve roles distribution.', 500, $e->getMessage());
        }
    }

    /**
     * Return the 5 most recently registered users.
     *
     * GET /api/dashboard/recent-users
     */
    public function recentUsers(): JsonResponse
    {
        try {
            $users = $this->dashboardService->getRecentUsers();

            return $this->successResponse($users, 'Recent users retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve recent users.', 500, $e->getMessage());
        }
    }

    /**
     * Return the 10 most recent activity log entries.
     *
     * GET /api/dashboard/recent-activities
     */
    public function recentActivities(): JsonResponse
    {
        try {
            $activities = $this->dashboardService->getRecentActivities();

            return $this->successResponse($activities, 'Recent activities retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve recent activities.', 500, $e->getMessage());
        }
    }

    /**
     * Return the 10 users who logged in most recently.
     *
     * GET /api/dashboard/latest-logins
     */
    public function latestLogins(): JsonResponse
    {
        try {
            $users = $this->dashboardService->getLatestLogins();

            return $this->successResponse($users, 'Latest logins retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve latest logins.', 500, $e->getMessage());
        }
    }
}
