<?php

namespace App\Exceptions;

use Exception;
use App\Http\HttpStatus;
use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class HandlerException extends Exception
{
    use ApiResponse;

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request, Throwable $e)
    {
        if ($request->expectsJson()) {

            $result = [
                'code' => HttpStatus::INTERNAL_SERVER_ERROR,
                'message' => 'Something went wrong',
                'payload' => null,
            ];

            $result = match (true) {
                $e instanceof ValidationException => [
                    'code' => HttpStatus::UNPROCESSABLE_ENTITY,
                    'message' => 'Validation Error',
                    'payload' => $e->errors(),
                ],

                $e instanceof AuthenticationException => [
                    'code' => HttpStatus::UNAUTHORIZED,
                    'message' => 'Unauthorized',
                ],

                $e instanceof AuthorizationException => [
                    'code' => HttpStatus::FORBIDDEN,
                    'message' => 'Forbidden',
                ],

                $e instanceof AccessDeniedHttpException => [
                    'code' => HttpStatus::FORBIDDEN,
                    'message' => 'Forbidden',
                ],

                $e instanceof UnauthorizedHttpException => [
                    'code' => HttpStatus::UNAUTHORIZED,
                    'message' => 'Unauthorized',
                ],

                $e instanceof ModelNotFoundException => [
                    'code' => HttpStatus::NOT_FOUND,
                    'message' => 'Resource not found',
                ],

                $e instanceof NotFoundHttpException => [
                    'code' => HttpStatus::NOT_FOUND,
                    'message' => 'Resource not found',
                ],

                $e instanceof MethodNotAllowedException => [
                    'code' => HttpStatus::METHOD_NOT_ALLOWED,
                    'message' => 'Method not allowed',
                ],

                $e instanceof ThrottleRequestsException => [
                    'code' => HttpStatus::TOO_MANY_REQUESTS,
                    'message' => 'Too many requests',
                ],

                $e instanceof HttpException => [
                    'code' => HttpStatus::INTERNAL_SERVER_ERROR,
                    'message' => 'Internal Server Error',
                ],

                default => $result,
            };

            return $this->errorResponse(
                $result['code'],
                $e->getMessage() ?: $result['message'],
                $result['payload'] ?? null,
            );
        }

        // For non-JSON requests, rethrow the exception to let the default handler deal with it
        throw $e;
    }
}
