<?php

namespace Tests\Feature\Users;

use App\Models\Users;
use App\Models\Users as UsersModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersControllerTest extends TestCase
{
    /**
     * @testdox Realizar login
     */
    public function testAuthLogin()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);

        $response = $this->post('api/users/change-password', [
            'old_password' => 'password',
            'new_password' => 'new_password',
        ], [
            'Authorization' => 'Bearer ' . $user->access_token
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue(!!$responseData['data']);
    }
}
