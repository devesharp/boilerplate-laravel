<?php

namespace App\Http\Controllers;

class AuthController extends Controller
{
    public function __construct(
        protected \App\Services\AuthService $service
    )
    {
    }


    public function login()
    {
        return $this->service->login();
    }

    public function forgetPassword()
    {
        return $this->service->forgetPassword(\request()->all()["login"] ?? "");
    }

    public function changePasswordByToken()
    {
        return $this->service->changePasswordByToken(\request()->all());
    }

    public function me()
    {
        return $this->service->me();
    }

    public function logout()
    {
        return $this->service->logout();
    }

    public function refresh()
    {
        return $this->service->refresh();
    }
}
