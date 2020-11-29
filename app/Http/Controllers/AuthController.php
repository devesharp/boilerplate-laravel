<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    protected \App\Services\Auth $service;

    public function __construct(\App\Services\Auth $service)
    {
        $this->service = $service;
    }

    public function login(){
        return $this->service->login();
    }

    public function me(){
        return $this->service->me();
    }

    public function logout(){
        return $this->service->logout();
    }

    public function refresh(){
        return $this->service->refresh();
    }
}
