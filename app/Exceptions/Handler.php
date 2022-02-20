<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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

        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof MethodNotAllowedHttpException) {
                $this->sendResponse($request, 404, $e->getMessage());
            } elseif ($e instanceof TokenInvalidException || $e instanceof TokenExpiredException || $e instanceof JWTException) {
                $this->sendResponse($request, 401, $e->getMessage());
            }
        });
    }

    public function sendResponse($request, $code, $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $message], $code);
        }

        abort($code, $message);
    }
}
