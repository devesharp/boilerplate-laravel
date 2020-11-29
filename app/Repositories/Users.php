<?php

namespace App\Repositories;

use Illuminate\Database\Capsule\Manager;

/**
 * Class Users.
 *
 * @method public                 Builder getModel()
 * @method \App\Models\Users findById($id, $enabled = true)
 * @method \App\Models\Users findIdOrFail($id, $enabled = true)
 */
class Users extends \Devesharp\RepositoryMysql
{
    /**
     * @var string
     */
    protected $model = \App\Models\Users::class;
}
