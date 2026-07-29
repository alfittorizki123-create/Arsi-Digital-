<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $module, string $description, array $properties = [], ?Request $request = null): void
    {
        try {
            $request = $request ?: request();
            $user = Auth::user();

            ActivityLog::create([
                'user_id' => $user?->id,
                'username' => $user?->username,
                'name' => $user?->name,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'properties' => empty($properties) ? null : $properties,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}