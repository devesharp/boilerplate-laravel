<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
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

    public function render($request, Throwable $e)
    {
        return response()->json([
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'status_code' => $this->getExceptionHTTPStatusCode($e),
            //'line' => app()->environment('prod') ? null : $e->getTrace(),
        ], $this->getExceptionHTTPStatusCode($e));
    }

    protected function getExceptionHTTPStatusCode($e) {
        if ($e instanceof AuthenticationException) {
            return 401;
        }

        return method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
    }
}
