<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
    if ($exception instanceof QueryException && !config('app.debug')) {
        return ResponseHelper::Response(false, $exception->getPrevious()->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, 'DB_NOT_FOUND', 'code');
    }

    return parent::render($request, $exception);
    }
}
