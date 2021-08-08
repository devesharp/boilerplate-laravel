<?php

namespace App\Policies;

use App\Interfaces\PermissionsEnum;
use App\Interfaces\RolesEnum;
use App\Models\Users;
use Devesharp\CRUD\Validator;

class UsersPolicy extends Validator
{
    public function create(Users $request)
    {
        $allow = false;
        if ($request->hasPermission(PermissionsEnum::PERMISSION_USERS_CREATE)) {
            $allow = true;
        }

        if (!$allow) {
         \Devesharp\CRUD\Exception::Unauthorized();
        }
    }

    public function update(Users $request, $model)
    {
        $allow = false;
        if ($request->hasPermission(PermissionsEnum::PERMISSION_USERS_CREATE)) {
            $allow = true;
        }

        if (!$allow) {
         \Devesharp\CRUD\Exception::Unauthorized();
        }
    }

    public function get(Users $request, $model)
    {
        $allow = false;
        if ($request->hasPermission(PermissionsEnum::PERMISSION_USERS_VIEW)) {
            $allow = true;
        }

        if (!$allow) {
         \Devesharp\CRUD\Exception::Unauthorized();
        }
    }

    public function search(Users $request)
    {
        $allow = false;
        if ($request->hasPermission(PermissionsEnum::PERMISSION_USERS_VIEW)) {
            $allow = true;
        }

        if (!$allow) {
         \Devesharp\CRUD\Exception::Unauthorized();
        }
    }

    public function delete(Users $request, $model)
    {
        $allow = false;
        if ($request->hasPermission(PermissionsEnum::PERMISSION_USERS_DELETE)) {
            $allow = true;
        }

        if (!$allow) {
         \Devesharp\CRUD\Exception::Unauthorized();
        }
    }
}
