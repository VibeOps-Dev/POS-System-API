<?php

namespace App\Services\System;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryLoggerService
{
    public function register(): void
    {
        if (!app()->environment(['local', 'development'])) {
            return;
        }

        DB::listen(function ($query) {
            $sql = $query->sql;

            foreach ($query->bindings as $binding) {
                // Convert DateTime
                if ($binding instanceof \DateTimeInterface) {
                    $binding = $binding->format('Y-m-d H:i:s');
                }

                // Convert boolean
                if (is_bool($binding)) {
                    $binding = $binding ? 1 : 0;
                }

                // Convert null
                if (is_null($binding)) {
                    $binding = 'NULL';
                } else {
                    // Wrap non-numeric
                    $binding = is_numeric($binding) ? $binding : "'{$binding}'";
                }
                $sql = preg_replace('/\?/', $binding, $sql, 1);
            }

            Log::channel('queryLog')->info($sql, [
                'time_ms' => $query->time,
                'path' => request()->path(),
                'method' => request()->method(),
            ]);
        });
    }
}
