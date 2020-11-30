<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected \App\Services\Users $service;

    public function __construct(\App\Services\Users $service)
    {
        $this->service = $service;
    }

    public function changePassword()
    {
        return $this->service->changePassword(
            \auth()->user(),
            \request()->all()
        );
    }
}
