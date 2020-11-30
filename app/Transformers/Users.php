<?php

namespace App\Transformers;

use Devesharp\CRUD\Transformer;

class Users extends \Devesharp\CRUD\Transformer
{
    public string $model = \App\Models\Users::class;

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

        return $transform;
    }
}
