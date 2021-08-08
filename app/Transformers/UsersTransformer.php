<?php

namespace App\Transformers;

use App\Services\UsersPermissionsService;
use Devesharp\CRUD\Transformer;

class UsersTransformer extends \Devesharp\CRUD\Transformer
{
    public string $model = \App\Models\Users::class;

    public function __construct(
        protected UsersPermissionsService $usersPermissionsService
    ) {
    }

    /**
     * @param $model
     * @param string $context
     * @param null $requester
     * @return mixed
     * @throws \Exception
     */
    public function transform($model, string $context = "default", $requester = null)
    {
        if (! $model instanceof $this->model) {
            throw new \Exception("invalid model transform");
        }

        $transform = $model->toArray();

        $transform["updated_at"] = (string) $model->updated_at;
        $transform["created_at"] = (string) $model->created_at;

        $transform["permissions"] = $this->usersPermissionsService->getPermissions($model);

        return $transform;
    }
}
