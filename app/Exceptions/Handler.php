<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

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

        // Handle API exceptions
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return $this->errorResponse(
                        $e->getMessage(),
                        422,
                        $e->errors()
                    );
                }

                if ($e instanceof AuthenticationException) {
                    return $this->errorResponse(
                        'Avtorizatsiyadan o\'tilmagan',
                        401
                    );
                }

                if ($e instanceof UnauthorizedException) {
                    return $this->errorResponse(
                        'Sizda bu amalni bajarish uchun ruxsat yo\'q',
                        403
                    );
                }

                if ($e instanceof ModelNotFoundException) {
                    return $this->errorResponse(
                        'Ma\'lumot topilmadi',
                        404
                    );
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return $this->errorResponse(
                        'Ma\'lumot topilmadi',
                        404
                    );
                }

                if ($e instanceof AuthorizationException) {
                    return $this->errorResponse(
                        'Sizda bu amalni bajarish uchun ruxsat yo\'q',
                        403
                    );
                }

                // Log unexpected errors
                Log::error($e);

                if (config('app.debug')) {
                    return $this->errorResponse(
                        $e->getMessage(),
                        500
                    );
                }

                return $this->errorResponse(
                    'Serverda xatolik yuz berdi',
                    500
                );
            }
        });
    }
}
