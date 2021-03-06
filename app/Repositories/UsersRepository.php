<?php

namespace App\Repositories;

use Devesharp\CRUD\Repository\RepositoryMysql;

/**
 * Class Users.
 *
 * @method public                 Builder getModel()
 * @method \App\Models\Users findById($id, $enabled = true)
 * @method \App\Models\Users findIdOrFail($id, $enabled = true)
 */
class UsersRepository extends RepositoryMysql
{
    /**
     * @var string
     */
    protected $model = \App\Models\Users::class;
}
