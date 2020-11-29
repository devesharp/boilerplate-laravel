<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * @testdox Realizar login
     */
    public function testAuthLogin()
    {
        $user = User::factory()->create();

        $response = $this->post('api/auth/login', [
            'login' => $user->login,
            'password' => 'password',
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue(is_string($responseData['access_token']));
    }

    /**
     * @testdox Login com dados incorretos
     */
    public function testLoginIncorrect()
    {
        $user = User::factory()->create();

        $response = $this->post('api/auth/login', [
            'login' => $user->login,
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @testdox Acessar rota privada
     */
    public function testPrivateRoute()
    {
        $user = User::factory()->create();

        $response = $this->post('api/auth/login', [
            'login' => $user->login,
            'password' => 'password',
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response = $this->get('api/auth/me', [
            'Authorization' => 'Bearer ' . $responseData['access_token']
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals($responseData['name'], $user->name);
        $this->assertEquals($responseData['email'], $user->email);
        $this->assertEquals($responseData['login'], $user->login);

    }
}
