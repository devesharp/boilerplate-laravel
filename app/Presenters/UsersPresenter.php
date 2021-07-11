<?php

namespace App\Presenters;

use Devesharp\CRUD\Presenter\Presenter;

class UsersPresenter extends Presenter {

    public function fullName()
    {
        return $this->name;
    }
}

