<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected \App\Services\Auth $service
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
