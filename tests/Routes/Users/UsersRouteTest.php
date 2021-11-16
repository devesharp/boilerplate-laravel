<?php

namespace Tests\Routes\Users;

use App\Interfaces\RolesEnum;
use App\Models\Users;
use App\Services\UsersPermissionsService;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersRouteTest extends TestCase
{
    /**
     * @testdox [POST]  api/users
     */
    public function testRouteUsersCreate()
    {
        $user = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($user, RolesEnum::ADMIN());

        $user->access_token = JWTAuth::fromUser($user);
        $UsersData = Users::factory()->raw();

        $response = $this->withPost([
            'name' => 'Criar usuário',
            'group' => ['Users'],
            'uri' => 'api/users',
            'data' => $UsersData,
            'headers' => [
                'Authorization' => 'Bearer ' . $user->access_token
            ]
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft(\Illuminate\Support\Arr::except($UsersData, ['password']), $responseData['data']);
    }

    /**
     * @testdox [POST]  api/users/:id
     */
    public function testRouteUsersUpdate()
    {
        $user = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($user, RolesEnum::ADMIN());

        $user->access_token = JWTAuth::fromUser($user);
        $UsersData = Users::factory()->raw();
        $resource = Users::factory()->create();

        $response = $this->withPost([
            'name' => 'Atualizar usuário',
            'group' => ['Users'],
            'uri' => 'api/users/:id',
            'params' => [
                [
                    'name' => 'id',
                    'value' => $resource->id,
                ]
            ],
            'data' => $UsersData,
            'headers' => [
                'Authorization' => 'Bearer ' . $user->access_token
            ]
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft(\Illuminate\Support\Arr::except($UsersData, ['password']), $responseData['data']);
    }

    /**
     * @testdox [GET]   api/users/:id
     */
    public function testRouteUsersGet()
    {
        $user = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($user, RolesEnum::ADMIN());

        $user->access_token = JWTAuth::fromUser($user);

        $resource = Users::factory()->create();

        $response = $this->withGet([
            'name' => 'Resgatar usuário',
            'group' => ['Users'],
            'uri' => 'api/users/:id',
            'params' => [
                [
                    'name' => 'id',
                    'value' => $resource->id,
                ]
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $user->access_token
            ]
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft(\Illuminate\Support\Arr::except($resource->getAttributes(), ['password', 'id']), $responseData['data']);
    }

    /**
     * @testdox [POST]  api/users/search
     */
    public function testRouteUsersSearch()
    {
        $user = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($user, RolesEnum::ADMIN());

        $user->access_token = JWTAuth::fromUser($user);
        $resource = Users::factory()->create();

        $response = $this->withPost([
            'name' => 'Buscar usuários',
            'group' => ['Users'],
            'uri' => 'api/users/search',
            'data' => [
                'filters' => [
                    'noGetMe' => true
                ]
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $user->access_token
            ],
            'validatorClass' => \App\Validators\UsersValidator::class,
            'validatorMethod' => 'search',
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals(1, $responseData['data']['count']);
        $this->assertEquals(1, count($responseData['data']['results']));
    }

    /**
     * @testdox [DELETE] api/users/:id
     */
    public function testRouteUsersDelete()
    {
        $user = Users::factory()->create();
        app(UsersPermissionsService::class)->setPermissionDefault($user, RolesEnum::ADMIN());

        $user->access_token = JWTAuth::fromUser($user);

        $resource = Users::factory()->create();

        $response = $this->withDelete([
            'name' => 'Deletar usuários',
            'group' => ['Users'],
            'uri' => 'api/users/:id',
            'params' => [
                [
                    'name' => 'id',
                    'value' => $resource->id,
                ]
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $user->access_token
            ],
            'validatorClass' => \App\Validators\UsersValidator::class,
            'validatorMethod' => 'delete',
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertTrue(!!$responseData['data']);
    }

    public function testUserChangePassword()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);

        $response = $this->withPost([
            'name' => 'Trocar senha',
            'group' => ['Users'],
            'uri' => "api/users/change-password",
            'data' => [
                "old_password" => "password",
                "new_password" => "new_password",
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $user->access_token
            ],
            'validatorClass' => \App\Validators\UsersValidator::class,
            'validatorMethod' => 'delete',
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue((bool) $responseData["data"]);
    }
}
