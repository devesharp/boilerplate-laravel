<?php

namespace Tests\Routes\Users;

use App\Models\Users;
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
        $user->access_token = JWTAuth::fromUser($user);
        $UsersData = Users::factory()->raw();

        $response = $this->post('api/users', $UsersData, [
            'Authorization' => 'Bearer ' . $user->access_token
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($UsersData, $responseData['data']);
    }

    /**
     * @testdox [POST]  api/users/:id
     */
    public function testRouteUsersUpdate()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $UsersData = Users::factory()->raw();
        $resource = Users::factory()->create();

        $response = $this->post('api/users/' . $resource->id, $UsersData, [
            'Authorization' => 'Bearer ' . $user->access_token
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($UsersData, $responseData['data']);
    }

    /**
     * @testdox [GET]   api/users/:id
     */
    public function testRouteUsersGet()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);

        $resource = Users::factory()->create();

        $response = $this->get('api/users/' . $resource->id, [
            'Authorization' => 'Bearer ' . $user->access_token
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($resource->getAttributes(), $responseData['data']);
    }

    /**
     * @testdox [POST]  api/users/search
     */
    public function testRouteUsersSearch()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $resource = Users::factory()->create();

        var_dump($resource->present()->fullName);


        $response = $this->post('api/users/search', [
            'filters' => [
                'noGetMe' => true
            ]
        ], [
            'Authorization' => 'Bearer ' . $user->access_token
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
        $user->access_token = JWTAuth::fromUser($user);

        $resource = Users::factory()->create();

        $response = $this->delete('api/users/' . $resource->id, [], [
            'Authorization' => 'Bearer ' . $user->access_token
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertTrue(!!$responseData['data']);
    }
}
