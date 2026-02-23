<?php

namespace App\Services\System;

use App\Exceptions\CustomException;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class TryCatchService
{
    use ApiResponse;

    public static function run(callable $callback)
    {
        try {
            return $callback();
        } catch (CustomException $e) {

            // Log error
            Log::channel('errorLog')->error($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . ' With Exception: ' . get_class($e) );
            $self = new static();

            return $self->errorResponse(
                $e->getCode() ?: 500,
                $e->getMessage(),
            );
        } catch (Throwable $th) {
            Log::channel('errorLog')->error($th->getMessage() . ' in ' . $th->getFile() . ':' . $th->getLine() . ' With Exception: ' . get_class($th) );

            throw $th;
        }
    }
}
