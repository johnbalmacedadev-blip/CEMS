<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    /**
     * In production, never render exception details/stack traces to the browser.
     * Instead, log the real exception and show a generic error page.
     */
    public function render($request, Throwable $e)
    {
        $shouldSuppressDetails = app()->environment('production') || $e instanceof \Illuminate\Database\QueryException;
        if (! $shouldSuppressDetails) {
            return parent::render($request, $e);
        }

        // Always log the exception server-side for debugging.
        try {
            $errorId = (string) \Illuminate\Support\Str::uuid();
            Log::error('App exception (suppressed in browser)', [
                'error_id' => $errorId,
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
            ]);
        } catch (Throwable $logFailure) {
            $errorId = null;
            Log::error('App exception (suppressed in browser) - logging failed');
        }

        // If the client expects JSON, return a minimal payload.
        if ($request instanceof Request && $request->expectsJson()) {
            return response()->json([
                'message' => 'An unexpected server error occurred.',
                'error_id' => $errorId,
            ], 500);
        }

        $status = 500;
        if (is_object($e) && method_exists($e, 'getStatusCode')) {
            $status = (int) $e->getStatusCode();
        }

        // Use generic views only; avoid exposing exception messages.
        if ($status === 403 && view()->exists('errors.403')) {
            return response()->view('errors.403', ['error_id' => $errorId], 403);
        }

        if (view()->exists('errors.500')) {
            return response()->view('errors.500', ['error_id' => $errorId], 500);
        }

        return response('An unexpected server error occurred.', $status);
    }
}





















