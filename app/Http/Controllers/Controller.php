<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 *
 * Base application controller extension.
 * Provides general endpoints such as health checks.
 */
class Controller extends BaseController
{
    /**
     * Health check endpoint.
     *
     * Returns a JSON response indicating the API is reachable.
     */
    public function ping(): JsonResponse
    {
        return response()->json()->default(
            code: 'S001',
            message: 'success',
            userMessage: 'sucesso',
            data: ['ping' => 'pong']
        );
    }
}
