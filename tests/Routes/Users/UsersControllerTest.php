<?php

namespace Tests\Feature\Routes;

use App\Models\Users;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersControllerTest extends TestCase
{
    /**
     * @testdox Realizar login
     */
    public function testUserChangePassword()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);

        $response = $this->post(
            "api/users/change-password",
            [
                "old_password" => "password",
                "new_password" => "new_password",
            ],
            [
                "Authorization" => "Bearer " . $user->access_token,
            ],
        );

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue((bool) $responseData["data"]);
    }
}
