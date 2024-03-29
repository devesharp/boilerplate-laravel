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
    protected $dontFlash = ["password", "password_confirmation"];

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

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(["error" => "Unauthenticated."], 401);
    }

    public function render($request, Throwable $e)
    {
        $data = [
            "error" => $e->getMessage(),
            "code" => $e->getCode(),
            "status_code" => $this->getExceptionHTTPStatusCode($e),
        ];

        if (!app()->environment('prod', 'testing')) {
            $data['line'] = $e->getTrace();
        }


        return response()->json($data,
            $this->getExceptionHTTPStatusCode($e),
        );
    }

    protected function getExceptionHTTPStatusCode($e)
    {
        if ($e instanceof AuthenticationException) {
            return 401;
        }

        if ($e instanceof \Devesharp\CRUD\Exception) {
            if($e->getCode() === \Devesharp\CRUD\Exception::TOKEN_INVALID){
                return 401;
            }
        }

        return method_exists($e, "getStatusCode") ? $e->getStatusCode() : 500;
    }

    public function report(Throwable $exception)
    {
        /**
         * Enviar erro para sentry
         */
        if (!empty(env("SENTRY_LARAVEL_DSN")) && app()->environment(['prod']) ) {
            if ($this->shouldReport($exception) && app()->bound("sentry")) {
                app("sentry")->captureException($exception);
            }
        }

        parent::report($exception);
    }
}
