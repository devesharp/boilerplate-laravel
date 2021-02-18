<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct(
        protected \App\Services\UsersService $service
    ){
    }

    public function changePassword()
    {
        return $this->service->changePassword(\auth()->user(), \request()->all());
    }
}
