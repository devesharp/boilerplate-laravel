<?php

namespace Tests\Unit\UsersPermissions;

use App\Interfaces\RolesEnum;
use App\Models\Users;
use App\Services\UsersPermissionsService;
use Tests\TestCase;

class UsersPermissionsTest extends TestCase
{
    public \App\Services\UsersService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app('\App\Services\UsersService');
    }

    /**
     * @testdox create - default
     */
    public function testCreateUsersWithPermissions()
    {
        $userAdmin = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($userAdmin, RolesEnum::ADMIN());
        $UsersData = Users::factory()->raw();

        $resource = $this->service->create($UsersData, $userAdmin);

        $this->assertEqualsArrayLeft([
            'PERMISSION_USERS_CREATE' => false,
            'PERMISSION_USERS_UPDATE' => false,
            'PERMISSION_USERS_VIEW' => true,
            'PERMISSION_USERS_DELETE' => false,
        ], $resource['permissions']);
    }

    /**
     * @testdox update - default
     */
    public function testUpdateUsersWithPermissions()
    {
        $userAdmin = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($userAdmin, RolesEnum::ADMIN());

        $UsersData = Users::factory()->raw();

        $resource = $this->service->create($UsersData, $userAdmin);
        $resource = $this->service->update($resource['id'], [
            'permissions' => [
                'PERMISSION_USERS_CREATE' => true,
                'PERMISSION_USERS_UPDATE' => true,
                'PERMISSION_USERS_VIEW' => true,
                'PERMISSION_USERS_DELETE' => true,
            ]
        ], $userAdmin);

        $this->assertEqualsArrayLeft([
            'PERMISSION_USERS_CREATE' => true,
            'PERMISSION_USERS_UPDATE' => true,
            'PERMISSION_USERS_VIEW' => true,
            'PERMISSION_USERS_DELETE' => true,
        ], $resource['permissions']);
    }
}
