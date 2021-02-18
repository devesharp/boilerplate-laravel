<?php

namespace App\Policies;

use Devesharp\CRUD\Validator;

class UsersPolicy extends Validator
{
    public function create($request)
    {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    public function update($request, $model)
    {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    public function get($request, $model)
    {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    public function search($request)
    {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    public function delete($request, $model)
    {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }
}
