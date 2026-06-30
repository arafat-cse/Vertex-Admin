<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogActivity
{
    /**
     * The HTTP methods that should be recorded.
     *
     * GET requests are intentionally excluded to avoid flooding the log with
     * read-only traffic. Only state-changing requests are captured.
     */
    private const LOGGABLE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Handle an incoming request.
     *
     * Passes the request to the next middleware/controller first, then — after
     * the response is built — writes a row to the activity_logs table when:
     *   - The request method is one of POST / PUT / PATCH / DELETE, and
     *   - A user is authenticated at the time of logging.
     *
     * Sensitive fields (password, password_confirmation, token) are stripped
     * from the recorded payload.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            in_array(strtoupper($request->method()), self::LOGGABLE_METHODS, true)
            && auth()->check()
        ) {
            $this->record($request, $response);
        }

        return $response;
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    /**
     * Persist the activity entry.
     *
     * Wrapped in a try/catch so that a logging failure never breaks the actual
     * response returned to the client.
     */
    private function record(Request $request, Response $response): void
    {
        try {
            $sensitiveFields = ['password', 'password_confirmation', 'token'];

            $payload = json_encode(
                $request->except($sensitiveFields),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            ActivityLog::create([
                'user_id'     => auth()->id(),
                'method'      => strtoupper($request->method()),
                'url'         => $request->fullUrl(),
                'ip'          => $request->ip(),
                'user_agent'  => $request->userAgent() ?? '',
                'status_code' => $response->getStatusCode(),
                'payload'     => $payload ?: '{}',
            ]);
        } catch (Throwable) {
            // Silently swallow logging errors so the response is never affected.
        }
    }
}
