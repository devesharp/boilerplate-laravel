<?php

namespace Tests\Unit\Users;

use App\Interfaces\RolesEnum;
use App\Models\Users;
use App\Services\UsersPermissionsService;
use Tests\TestCase;

class UsersTest extends TestCase
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
    public function testCreateUsers()
    {
        $userAdmin = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($userAdmin, RolesEnum::ADMIN());
        $UsersData = Users::factory()->raw();

        $resource = $this->service->create($UsersData, $userAdmin);

        $this->assertGreaterThanOrEqual(1, $resource['id']);
        $this->assertEqualsArrayLeft(\Illuminate\Support\Arr::except($UsersData, ['password']), $resource);
    }

    /**
     * @testdox update - default
     */
    public function testUpdateUsers()
    {
        $userAdmin = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($userAdmin, RolesEnum::ADMIN());
        $UsersData = Users::factory()->raw();

        $resource = $this->service->create($UsersData, $userAdmin);

        $UsersDataUpdate = Users::factory()->raw();

        $resourceUpdated = $this->service->update($resource['id'], $UsersDataUpdate, $userAdmin);

        $this->assertEqualsArrayLeft(\Illuminate\Support\Arr::except($UsersDataUpdate, ['password']), $resourceUpdated);
    }

    /**
     * @testdox get - default
     */
    public function testGetUsers()
    {
        $userAdmin = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($userAdmin, RolesEnum::ADMIN());
        $UsersData = Users::factory()->raw();

        $resourceCreated = $this->service->create($UsersData, $userAdmin);

        $resource = $this->service->get($resourceCreated['id'], $userAdmin);

        $this->assertGreaterThanOrEqual(1, $resource['id']);
        $this->assertEqualsArrayLeft(\Illuminate\Support\Arr::except($UsersData, ['password']), $resource);
    }

    /**
     * @testdox search - default
     */
    public function testSearchUsers()
    {
        $userAdmin = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($userAdmin, RolesEnum::ADMIN());
        Users::factory()->count(5)->create();

        $results = $this->service->search([
            "filters" => [
                "id" => 1
            ]
        ], $userAdmin);
        $this->assertEquals(1, $results['count']);

    }

    /**
     * @testdox delete - default
     */
    public function testDeleteUsers()
    {
        $userAdmin = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($userAdmin, RolesEnum::ADMIN());
        $UsersData = Users::factory()->raw();

        $resource = $this->service->create($UsersData, $userAdmin);

        $this->service->delete($resource['id'], $userAdmin);

//        // If softDelete = false
//        $this->assertNull(Users::query()->find($resource['id']));

        // If softDelete = true
        $this->assertFalse(!!Users::query()->find($resource['id'])->enabled);
    }
}
