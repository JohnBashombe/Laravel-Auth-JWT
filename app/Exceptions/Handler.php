<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        //
        });
    }

    public function render($request, Throwable $exception)
    {
        $statusCode = 400;
        $errorMessage = "error";

        if ($request->is('*')) {

            $errors = $exception->getMessage();

            if ($errors === 'Route [login] not defined.') {
                $errorMessage = "Unauthorized";
                $statusCode = 401;
            }
            else if (
            $errors === 'The POST method is not supported for this route. Supported methods: GET, HEAD.' ||
            $errors === 'The PUT method is not supported for this route. Supported methods: GET, HEAD.' ||
            $errors === 'The DELETE method is not supported for this route. Supported methods: GET, HEAD.' ||
            $errors === 'The PATCH method is not supported for this route. Supported methods: GET, HEAD.' ||
            $errors === ''
            ) {
                $errorMessage = "Page Not Found";
                $statusCode = 404;
            }
            else if ($errors === 'Too Many Attempts.') {
                $errorMessage = "Too Many Attempts.";
                $statusCode = 429;
            }

            error_log($errors);

            return response()->json([
                'status' => $statusCode,
                'message' => $errorMessage
            ], $statusCode);
        }
        else {
            return response()->json([
                'status' => $statusCode,
                'message' => $errorMessage
            ], $statusCode);
        }
    }
}