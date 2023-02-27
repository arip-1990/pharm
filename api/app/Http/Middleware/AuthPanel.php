<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->role or $request->user()->role->isUser())
            return new JsonResponse(['message' => 'Доступ запрещен!'], Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
