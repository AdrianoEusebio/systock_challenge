<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class CheckJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'usuário inválido'
                ], 401);
            }
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'success' => false,
                    'message' => 'token inválido'
                ], 401);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json([
                    'success' => false,
                    'message' => 'token expirado'
                ], 401);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'usuário inválido'
                ], 401);
            }
        }

        return $next($request);
    }
}
