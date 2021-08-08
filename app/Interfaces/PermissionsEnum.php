<?php

namespace App\Interfaces;

use Devesharp\Support\Collection;

class PermissionsEnum
{
    public const PERMISSION_USERS_CREATE = 'PERMISSION_USERS_CREATE';
    public const PERMISSION_USERS_UPDATE = 'PERMISSION_USERS_UPDATE';
    public const PERMISSION_USERS_VIEW = 'PERMISSION_USERS_VIEW';
    public const PERMISSION_USERS_DELETE = 'PERMISSION_USERS_DELETE';

    /**
     * @return array
     */
    public static function getAllPermissions(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);

        return Collection::make(array_values($oClass->getConstants()))
            ->filter(fn ($value) => str_contains($value, 'PERMISSION'))
            ->toArray();
    }

    public static function getDefaultPermissions($permission): bool
    {
        $permissionDefault['PERMISSION_USERS_CREATE'] = false;
        $permissionDefault['PERMISSION_USERS_UPDATE'] = false;
        $permissionDefault['PERMISSION_USERS_VIEW'] = true;
        $permissionDefault['PERMISSION_USERS_DELETE'] = false;

        return $permissionDefault[$permission] ?? false;
    }
}
