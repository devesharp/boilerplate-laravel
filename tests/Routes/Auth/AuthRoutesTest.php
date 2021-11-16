<?php

namespace Tests\Routes\Auth;

use App\Models\Users;
use App\Models\UsersTokens;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    /**
     * @testdox Realizar login
     */
    public function testAuthLogin()
    {
        $user = Users::factory()->create();

        $response = $this->withPost([
            'name' => 'Realizar Login',
            'group' => ['Auth'],
            'uri' => 'api/auth/login',
            'data' => [
                "login" => $user->login,
                "password" => "password",
            ]
        ]);
        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue(is_string($responseData["data"]["access_token"]));
    }

    /**
     * @testdox Login com dados incorretos
     */
    public function testLoginIncorrect()
    {
        $user = Users::factory()->create();

        $response = $this->withPost([
            'name' => 'Realizar Login',
            'group' => ['Auth'],
            'uri' => 'api/auth/login',
            'data' => [
                "login" => $user->login,
                "password" => "password123",
            ],
            'response' => [
              'description' => 'Não autorizado'
            ]
        ]);

        $response->assertStatus(401);
    }

    /**
     * @testdox Acessar rota privada
     */
    public function testPrivateRoute()
    {
        $this->withoutExceptionHandling();

        $user = Users::factory()->create();

        $response = $this->post("api/auth/login", [
            "login" => $user->login,
            "password" => "password",
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response = $this->get("api/auth/me", [
            "Authorization" => "Bearer " . $responseData["data"]["access_token"],
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals($responseData["data"]["name"], $user->name);
        $this->assertEquals($responseData["data"]["email"], $user->email);
        $this->assertEquals($responseData["data"]["login"], $user->login);
    }

    /**
     * @testdox Não deve conseguir acessar com token com enabled = false
     */
    public function testPrivateRouteWithInvalidToken()
    {
        $user = Users::factory()->create();

        $response = $this->post("api/auth/login", [
            "login" => $user->login,
            "password" => "password",
        ]);

        $responseData = json_decode($response->getContent(), true);

        // Desabilitar tokens
        UsersTokens::query()->update(['enabled' => false]);

        $response = $this->get("api/auth/me", [
            "Authorization" => "Bearer " . $responseData["data"]["access_token"],
        ]);

        $response->assertStatus(401);
    }

    /**
     * @testdox Não deve conseguir acessar quando token não existe
     */
    public function testPrivateRouteWithTokenNotExist()
    {
        $user = Users::factory()->create();

        $response = $this->post("api/auth/login", [
            "login" => $user->login,
            "password" => "password",
        ]);

        $responseData = json_decode($response->getContent(), true);

        // Desabilitar tokens
        UsersTokens::query()->delete();

        $response = $this->get("api/auth/me", [
            "Authorization" => "Bearer " . $responseData["data"]["access_token"],
        ]);

        $response->assertStatus(401);
    }

    public function testAdminPasswordRecover()
    {
        $user = Users::factory()->create();

        $response = $this->withPost([
            'name' => 'Gerar token de recuperar senha',
            'group' => ['Auth'],
            'uri' => 'api/auth/password-recover',
            'data' => [
                "login" => $user->login
            ]
        ]);

        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData["success"]);
        $this->assertEquals($user->email, $responseData["data"]["email"]);
    }

    public function testAdminPasswordRecoverError()
    {
        $response = $this->withPost([
            'name' => 'Gerar token de recuperar senha',
            'group' => ['Auth'],
            'uri' => 'api/auth/password-recover',
            'data' => [
                "login" => "john"
            ],
            'response' => [
                'description' => 'Login incorreto'
            ]
        ]);

        $responseData = json_decode($response->getContent(), true);

        $this->assertFalse($responseData["success"]);
    }

    public function testAdminPasswordReset()
    {
        $user = Users::factory()->create([
            "remember_token" => "remember_token",
        ]);

        $response =  $this->withPost([
            'name' => 'Mudar senha com token de recuperar senha',
            'group' => ['Auth'],
            'uri' => 'api/auth/password-reset/remember_token',
            'data' => [
                "remember_token" => "remember_token",
                "password" => "123456",
            ]
        ]);

        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData["success"]);
        $this->assertTrue((bool) $responseData["data"]);
    }

    /**
     * @testdox Atualizar hash
     */
    public function testRefresh()
    {
        $user = Users::factory()->create();

        $response = $this->post("api/auth/login", [
            "login" => $user->login,
            "password" => "password",
        ]);

        $responseData = json_decode($response->getContent(), true);

        $response = $this->post("api/auth/refresh", [
            "Authorization" => "Bearer " . $responseData["data"]["access_token"],
        ]);

        $responseDataNew = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertNotEquals($responseData["data"]["access_token"], $responseDataNew["data"]["access_token"]);
    }
}
