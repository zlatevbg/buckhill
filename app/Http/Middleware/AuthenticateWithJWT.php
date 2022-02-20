<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Auth\Access\AuthorizationException;

class AuthenticateWithJWT extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $jwt = JWTAuth::parseToken();
        $user = $jwt->authenticate();

        if ($user) {
            $token = $user->token;

            if (!$token) {
                throw new TokenInvalidException('Authorization Token not found');
            } elseif ($token->unique_id != hash('sha256', $jwt->getToken())) {
                throw new TokenExpiredException('Token is Expired');
            }
        } else {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
