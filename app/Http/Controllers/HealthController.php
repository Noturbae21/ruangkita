<?php

namespace App\Http\Controllers;

use App\Support\Clock;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    /**
     * FR-13 & K10 Rollback Smoke Test:
     * GET /health returns HTTP 200 and database connection status, or 503 if unhealthy.
     */
    public function __invoke(): JsonResponse
    {
        $forceUnhealthy = filter_var(env('FORCE_UNHEALTHY', false), FILTER_VALIDATE_BOOLEAN);

        if ($forceUnhealthy) {
            return response()->json([
                'status' => 'unhealthy',
                'database' => 'forced_unhealthy',
                'message' => 'Service forced unhealthy via FORCE_UNHEALTHY environment flag.',
                'timestamp' => Clock::nowWib()->toIso8601String(),
            ], 503);
        }

        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');

            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'timestamp' => Clock::nowWib()->toIso8601String(),
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'unhealthy',
                'database' => 'disconnected',
                'message' => 'Database connection failed.',
                'timestamp' => Clock::nowWib()->toIso8601String(),
            ], 503);
        }
    }
}
