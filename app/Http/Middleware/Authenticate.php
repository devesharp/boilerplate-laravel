<?php

namespace App\Http\Middleware;

use App\Exceptions\Exception;
use App\Models\UsersTokens;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param mixed ...$guards
     * @return mixed
     * @throws \Devesharp\CRUD\Exception
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $token = auth()->payload()->toArray()['t'] ?? '';

        $tokenValid = UsersTokens::query()->where('token', $token)
            ->where('enabled', true)
            ->count();

        if ($tokenValid !== 1) {
            Exception::Exception(Exception::TOKEN_INVALID);
        }

        return $next($request);
    }
}
