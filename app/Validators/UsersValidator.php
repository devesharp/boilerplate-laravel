<?php

namespace App\Validators;

use Devesharp\CRUD\Validator;

class UsersValidator extends Validator
{
    protected array $rules = [
        "create" => [
            "name" => "string|max:100|required",
            "login" => "string|max:100|required",
            "email" => "string|max:100|required",
            "password" => "string|max:100|required",
            "document" => "string|max:100",
            "image" => "string|max:100",
            'permissions' => 'array',
        ],
        "update" => [
            "_extends" => "create",
            "id" => "numeric",
            "password" => null,
        ],
        // Busca
        "search" => [
            "filters.id" => "numeric",
            "filters.name" => "string",
            "filters.noGetMe" => "boolean",
        ],
        /*
         * Mudar senha
         */
        "change_password" => [
            "old_password" => "required|string|max:100",
            "new_password" => "required|string|max:100",
        ],
        /*
         * Mudar senha por token
         */
        "change_password_token" => [
            "remember_token" => "required|string",
            "password" => "required|string|max:100",
        ],
    ];

    public function create(array $data, $requester = null)
    {
        $context = "create";

        return $this->validate($data, $this->getValidate($context));
    }

    public function update(array $data, $requester = null)
    {
        $context = "update";

        return $this->validate($data, $this->removeRequiredRules($this->getValidate($context)));
    }

    public function search(array $data, $requester = null)
    {
        return $this->validate($data, $this->getValidateWithSearch("search"));
    }

    /**
     * @param  array                   $data
     * @throws \App\Handlers\Exception
     * @return mixed
     */
    public function changePasswordByToken(array $data)
    {
        return $this->validate($data, $this->getValidate("change_password_token"));
    }

    public function changePassword(array $data)
    {
        return $this->validate($data, $this->getValidate("change_password"));
    }
}
