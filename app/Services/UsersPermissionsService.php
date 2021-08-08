<?php

namespace App\Services;

use App\Interfaces\RolesEnum;
use App\Models\Users;
use App\Models\UsersPermissions;
use App\Interfaces\PermissionsEnum;

class UsersPermissionsService
{
    /**
     * get all permissions for user.
     * @param  Users|int    $user
     * @param  string|array $role
     * @return bool
     */
    public function getPermissions(Users | int $user): array
    {
        return UsersPermissions::query()
            ->where([
                'user_id' => $user->id ?? $user,
            ])
            ->get()
            ->mapWithKeys(fn ($item) => [$item['name'] => '1' == $item['value']])
            ->toArray();
    }

    /**
     * check user has permission.
     * @param  Users|int    $user
     * @param  string|array $role
     * @param  string|array $permissionCheck
     * @return bool
     */
    public function hasPermission(Users | int $user, string | array $permissionCheck): bool
    {
        if (is_int($user)) {
            $user = Users::query()->find($user);
        }

        // Usuário sistema tem todas as permissões
        if ($user->id == config('app.user_system_id')) {
            return true;
        }

        $permissions = $user->permissions;
        $allow = true;
        // Check all permissions is checked
        $permissionsChecked = 0;

        if (is_string($permissionCheck)) {
            $allow = false;
        }

        foreach ($permissions as $permission) {
            if (is_string($permissionCheck) && $permission->name === $permissionCheck) {
                return (bool) $permission->value;
            } elseif (is_array($permissionCheck) && in_array($permission->name, $permissionCheck)) {
                $allow = $allow && (bool) $permission->value;
                ++$permissionsChecked;
            }
        }

        if (is_array($permissionCheck) && $permissionsChecked != count($permissionCheck)) {
            return false;
        }

        return $allow;
    }

    /**
     * update one user permission.
     * @param Users  $user
     * @param string $role
     * @param $value
     */
    public function setPermission(Users $user, string $role, $value = true): void
    {
        if (in_array($role, PermissionsEnum::getAllPermissions())) {
            $permissionModel = UsersPermissions::query()->firstOrCreate([
                'user_id' => $user->id,
                'name' => $role,
            ]);

            $permissionModel->value = $value;
            $permissionModel->save();
        }
    }

    /**
     * set users permissions.
     * @param Users $user
     * @param array $initialPermission
     */
    public function setPermissions(Users $user, array $initialPermission = []): void
    {
        foreach (PermissionsEnum::getAllPermissions() as $permission) {
            $permissionModel = UsersPermissions::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $permission,
                ],
                [
                    'value' => PermissionsEnum::getDefaultPermissions($permission),
                ],
            );

            if (isset($initialPermission[$permission])) {
                $permissionModel->value = (bool) $initialPermission[$permission];
                $permissionModel->save();
            }
        }
    }

    /**
     * set users permissions.
     * @param Users $user
     * @param array $initialPermission
     */
    public function setPermissionDefault(Users $user, RolesEnum $role): void
    {
        $initialPermission = $this->getDefaultPermissions($role);

        foreach (PermissionsEnum::getAllPermissions() as $permission) {
            $permissionModel = UsersPermissions::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $permission,
                ],
                [
                    'value' => PermissionsEnum::getDefaultPermissions($permission),
                ],
            );

            if (isset($initialPermission[$permission])) {
                $permissionModel->value = (bool) $initialPermission[$permission];
                $permissionModel->save();
            }
        }
    }

    /**
     * get default permissions for specific roles.
     * @param  string $role
     * @return array
     */
    public function getDefaultPermissions(RolesEnum $role): array
    {
        switch ($role) {
            case RolesEnum::SIMPLE():
                return [
                    // Imóveis
                    PermissionsEnum::PERMISSION_USERS_CREATE => false,
                    PermissionsEnum::PERMISSION_USERS_UPDATE => false,
                    PermissionsEnum::PERMISSION_USERS_VIEW => true,
                    PermissionsEnum::PERMISSION_USERS_DELETE => false,
                ];

            case RolesEnum::ADMIN():
                return [
                    // Imóveis
                    PermissionsEnum::PERMISSION_USERS_CREATE => true,
                    PermissionsEnum::PERMISSION_USERS_UPDATE => true,
                    PermissionsEnum::PERMISSION_USERS_VIEW => true,
                    PermissionsEnum::PERMISSION_USERS_DELETE => true,
                ];
        }

        return [];
    }
    /**
     * Usuário só pode dar permissões que ele tem.
     * @param $permissions
     * @param $userPermissions
     * @return array
     */
    public function permissionGiverUsersChecker(array $permissions, array $userPermissions): array
    {
        // Usuário só pode dar permissões que ele tem
        foreach ($permissions as $permissionName => $permissionValue) {
            $permissions[$permissionName] =
                $permissions[$permissionName] &&
                isset($userPermissions[$permissionName]) &&
                $userPermissions[$permissionName];
        }

        return $permissions;
    }
}
