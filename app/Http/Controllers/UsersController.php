<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    protected ?Users $auth;

    public function __construct(
        protected \App\Services\UsersService $service
    ){
        $this->auth = function_exists('auth') ? auth()->user() : null;
    }

    public function search()
    {
        return $this->service->search(request()->all(), $this->auth);
    }

    public function get($id)
    {
        return $this->service->get($id, $this->auth);
    }

    public function update($id)
    {
        return $this->service->update($id, request()->all(), $this->auth);
    }

    public function create()
    {
        return $this->service->create(request()->all(), $this->auth);
    }

    public function delete($id)
    {
        return $this->service->delete($id, $this->auth);
    }

    public function changePassword()
    {
        return $this->service->changePassword($this->auth, \request()->all());
    }
}
