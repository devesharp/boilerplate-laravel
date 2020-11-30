<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Devesharp\CRUD\Exception;
use Devesharp\CRUD\Repository\RepositoryMysql;
use Devesharp\CRUD\Transformer;
use Illuminate\Support\Facades\Hash;

class Auth
{
    protected \App\Transformers\Users $transformer;
    protected \App\Repositories\Users $repository;
    protected \App\Validators\Users $validator;

    public function __construct(
        \App\Transformers\Users $transformer,
        \App\Repositories\Users $repository,
        \App\Validators\Users $validator
    ) {
        $this->transformer = $transformer;
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function login()
    {
        $credentials = request(["login", "password"]);

        if (! ($token = auth()->attempt($credentials))) {
            return response()->json(["error" => "Unauthorized"], 401);
        }

        $user = auth()->user();
        $user->access_token = $token;

        return Transformer::item($user, $this->transformer);
    }

    /**
     * @return mixed
     */
    public function me()
    {
        $user = auth()->user();

        return Transformer::item($user, $this->transformer);
    }

    /**
     * @param string $login
     * @param null $token
     * @return array
     * @throws Exception
     */
    public function forgetPassword(string $login, $token = null)
    {
        $token = $token ?? base64_encode(uniqid(rand(), true) . "-" . date("YmdHis"));

        if (empty($login)) {
            Exception::Exception(Exception::NOT_FOUND_RESOURCE);
        }

        // Resgatar usuário
        $user = $this->repository
            ->andWhere(function (RepositoryMysql $query) use ($login) {
                $query->orWhereLike("login", $login)->orWhereLike("email", $login);
            })
            ->findOne();

        if (empty($user)) {
            Exception::Exception(Exception::NOT_FOUND_RESOURCE);
        }

        // Adicionar token
        $this->repository->updateById($user->id, [
            "remember_token" => $token,
            "remember_token_at" => Carbon::now(),
        ]);

        // Enviar email

        return [
            "email" => $user->email,
        ];
    }

    /**
     * Mudar Senha da conta pelo token de esqueci a Senha.
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function changePasswordByToken(array $data)
    {
        $data = $this->validator->changePasswordByToken($data);

        $user = $this->repository
            ->clearQuery()
            ->whereSameString("remember_token", $data["remember_token"])
            ->findOne();

        // Token não existe
        if (empty($user)) {
            Exception::NotFound();
        }

        $user->remember_token = null;
        $user->password = Hash::make($data["password"]);
        $user->update();

        return true;
    }

    /**
     * @return bool[]
     */
    public function logout()
    {
        auth()->logout();

        return [
            "logout" => true,
        ];
    }

    /**
     * @return array
     */
    public function refresh()
    {
        return [
            "access_token" => auth()->refresh(),
        ];
    }
}
