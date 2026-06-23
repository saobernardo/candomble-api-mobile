<?php

namespace App\Exceptions;

use Error;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
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
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'retcode' => 'NOT_FOUND',
                'message' => $e->getMessage(),
                'errors' => $this->getTrace($e),
            ], 404);
        });

        $this->renderable(function (ClientException $e) {
            return response()->json([
                'retcode' => 'INVALID_PARAM',
                'message' => config('app.debug') ? $e->getMessage() : 'erro ao se comunicar com o cliente',
                'errors' => config('app.debug') ? $e->getResponse() : '',
            ], 400);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'retcode' => 'BAD_REQUEST',
                'message' => 'erro de validação',
                'errors' => config('app.debug') ? $e->errors() : '',
            ], 400);
        });

        $this->renderable(function (Exception $e) {
            return response()->json([
                'retcode' => 'SERVER_ERROR',
                'message' => config('app.debug') ? $e->getMessage() : 'Erro interno de servidor',
                'errors' => $this->getTrace($e),
            ], 500);
        });

        $this->renderable(function (Error $e) {
            return response()->json([
                'retcode' => 'SERVER_ERROR',
                'message' => config('app.debug') ? $e->getMessage() : 'Erro interno de servidor',
                'errors' => $this->getTrace($e),
            ], 500);
        });
    }

    /**
     * Get Trace
     *
     * @param  Exception|Error|NotFoundHttpException  $e
     *
     * @return array
     */
    private function getTrace(Exception|Error|NotFoundHttpException $e): array
    {
        return config('app.debug') ? $e->getTrace() : [];
    }
}
