<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

class Clock
{
    protected static ?CarbonInterface $customNow = null;

    /**
     * Inject a deterministic clock time for testing.
     */
    public static function setTestNow(CarbonInterface|string|null $now): void
    {
        if (is_string($now)) {
            $now = Carbon::parse($now);
        }

        self::$customNow = $now ? $now->copy()->utc() : null;
        Carbon::setTestNow(self::$customNow);
    }

    /**
     * Reset the injected clock.
     */
    public static function reset(): void
    {
        self::$customNow = null;
        Carbon::setTestNow();
    }

    /**
     * Get the current time in UTC, supporting clock injection via code or test headers/payload.
     */
    public static function now(?Request $request = null): Carbon
    {
        if ($request !== null) {
            $injected = $request->header('X-Test-Now')
                ?? $request->header('X-Mock-Time')
                ?? $request->input('_now')
                ?? $request->input('current_time');

            if (is_string($injected) && trim($injected) !== '') {
                return self::parseToUtc($injected);
            }
        }

        if (self::$customNow !== null) {
            return Carbon::instance(self::$customNow)->copy()->utc();
        }

        return Carbon::now('UTC');
    }

    /**
     * Get the current time in Asia/Jakarta (WIB).
     */
    public static function nowWib(?Request $request = null): Carbon
    {
        return self::now($request)->copy()->setTimezone('Asia/Jakarta');
    }

    /**
     * Parse a timestamp string (defaulting to Asia/Jakarta if no offset is specified) and convert to UTC.
     */
    public static function parseToUtc(string $value): Carbon
    {
        $trimmed = trim($value);

        // Check if string already carries an explicit timezone offset or 'Z'
        if (preg_match('/(Z|[+-]\d{2}:?\d{2})$/i', $trimmed)) {
            return Carbon::parse($trimmed)->utc();
        }

        return Carbon::parse($trimmed, 'Asia/Jakarta')->utc();
    }
}
