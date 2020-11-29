<?php

namespace App\Policies;

use Devesharp\CRUD\Validator;

class Users extends Validator
{
    function create($request) {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    function update($request, $model) {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    function get($request, $model) {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    function search($request) {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }

    function delete($request, $model) {
        // \Devesharp\CRUD\Exception::Unauthorized();
    }
}
