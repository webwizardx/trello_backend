<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Str;

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

    public function render($request, $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $model = (string) $e->getModel();
            $model = explode('\\', $model);
            $model = Str::of(end($model))->singular();
            $data = [
                'datetime' => now()->toDateTimeString(),
                'timestamp' => now()->toTimeString(),
                'ip' => $request->ip(),
                'message' => "$model not found",
                'statusCode' => 404
            ];

            return response()->json($data, 404);
        }

        return parent::render($request, $e);
    }
}
