<?php

namespace App\Services;

use App\Models\User;
use Devesharp\CRUD\Transformer;

class Auth
{
    protected \App\Transformers\Users $transformer;

    public function __construct(
        \App\Transformers\Users $transformer
    ) {
        $this->transformer = $transformer;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function login()
    {
        $credentials = request(['login', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        $user->access_token = $token;

        return Transformer::item($user, $this->transformer);
    }

    /**
     * @return mixed
     */
    public function me()
    {
        $user = auth()->user();

        return Transformer::item($user, $this->transformer);
    }

    /**
     * @return bool[]
     */
    public function logout()
    {
        auth()->logout();

        return [
            'logout' => true
        ];
    }

    /**
     * @return array
     */
    public function refresh()
    {
        return [
            'access_token' => auth()->refresh()
        ];
    }
}
