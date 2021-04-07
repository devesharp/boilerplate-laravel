<?php

namespace App\Http\Middleware;

use App\Models\UsersTokens;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $token = auth()->payload()->toArray()['t'] ?? '';

        $tokenValid = UsersTokens::query()->where('token', $token)
            ->where('enabled', true)
            ->count();

        if ($tokenValid !== 1) {
            throw new AuthenticationException();
        }

        return $next($request);
    }
}
