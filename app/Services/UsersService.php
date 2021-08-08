<?php

namespace App\Services;

use App\Utils\UsersRolesEnum;
use Devesharp\CRUD\Exception;
use Devesharp\CRUD\Service;
use Devesharp\CRUD\ServiceFilterEnum;
use Devesharp\CRUD\Transformer;
use Devesharp\Support\Collection;
use Devesharp\Support\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersService extends Service
{
    public array $sort = [
        "id" => [
            "column" => "id",
        ],
    ];

    public string $sort_default = "id";

    public array $filters = [
        // Filter default
        "id" => [
            "column" => "id",
            "filter" => ServiceFilterEnum::whereInt,
        ],
        "name" => [
            "column" => "name",
            "filter" => ServiceFilterEnum::whereContainsLike,
        ],
        // Filter column raw
        "full_name" => [
            "column" => "raw:(name || ' ' || age)",
            "filter" => ServiceFilterEnum::whereContainsExplodeString,
        ],
    ];

    public function __construct(
        protected \App\Validators\UsersValidator $validator,
        protected \App\Transformers\UsersTransformer $transformer,
        protected \App\Repositories\UsersRepository $repository,
        protected UsersPermissionsService $usersPermissionsService,
        protected \App\Policies\UsersPolicy $policy
    ) {
    }

    /**
     * Create resource.
     *
     * @param array $originalData
     * @param null $requester
     * @return mixed
     * @throws \Exception
     */
    public function create(array $originalData, $requester = null)
    {
        try {
            DB::beginTransaction();

            // Authorization
            $this->policy->create($requester);

            // Data validation
            $data = $this->validator->create($originalData, $requester);

            // Treatment data
            $resourceData = $this->treatment($requester, $data, null, "create");

            // Criar Usuário
            $model = $this->repository->create(Helpers::arrayExclude($resourceData->toArray(), ['permissions']));

            // Adiciona permissões dos usuários
            $this->usersPermissionsService->setPermissions($model, $resourceData['permissions'] ?? []);

            DB::commit();

            return $this->get($model->id, $requester);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     * @param array $originalData
     * @param null $requester
     * @return mixed
     * @throws \Exception
     */
    public function update(int $id, array $originalData, $requester = null)
    {
        try {
            DB::beginTransaction();

            $model = $this->repository->findIdOrFail($id);

            // Authorization
            $this->policy->update($requester, $model);

            // Data validation
            $data = $this->validator->update($originalData, $requester);

            // Treatment data
            $resourceData = $this->treatment($requester, $data, $model, "update");

            // Atualizar modelo
            $this->repository->updateById($id, \Devesharp\Support\Helpers::arrayExclude($data->toArray(), 'permissions'));

            // Atualiza permissões dos usuários
            $this->usersPermissionsService->setPermissions($model, $resourceData['permissions'] ?? []);

            DB::commit();

            return $this->get($id, $requester);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $requester
     * @param Collection $requestData
     * @param $currentModel
     * @param string $method
     * @return Collection
     */
    public function treatment($requester, Collection $requestData, $currentModel, string $method)
    {
        if ($method == "update") {
            if (! empty($requestData['permissions'])) {
                $requestData['permissions'] = $this->usersPermissionsService->permissionGiverUsersChecker(
                    $requestData['permissions'] ?? [],
                    $this->usersPermissionsService->getPermissions($requester),
                );
            }

            return $requestData;
        } elseif ($method == "create") {

            $requestData['permissions'] = $this->usersPermissionsService->getDefaultPermissions(
                \App\Interfaces\RolesEnum::SIMPLE(),
            );

            return $requestData;
        }

        return $requestData;
    }

    /**
     * @param int $id
     * @param $receiver
     * @param string $context
     * @return mixed
     * @throws \Devesharp\CRUD\Exception
     */
    public function get(int $id, $receiver)
    {
        // Get model
        $model = $this->repository->findIdOrFail($id);

        $this->policy->get($receiver, $model);

        return Transformer::item($model, $this->transformer, "default", $receiver);
    }

    /**
     * @param \App\Models\Users $user
     * @param array $originalData
     * @return bool
     * @throws Exception
     */
    public function changePassword(\App\Models\Users $user, array $originalData)
    {
        $data = $this->validator->changePassword($originalData);

        if (! Hash::check($data["old_password"], $user->password)) {
            \App\Exceptions\Exception::Exception(\App\Exceptions\Exception::PASSWORD_INCORRECT);
        }

        $user->password = Hash::make($data["new_password"]);
        $user->save();

        return true;
    }

    /**
     * @param array $originalData
     * @param null $requester
     * @return array
     */
    public function search(array $originalData = [], $requester = null)
    {
        // Authorization
        $this->policy->search($requester);

        // Validate data
        $data = $this->validator->search($originalData, $requester);

        // Make query
        $query = $this->makeSearch($data, $requester);

        return $this->transformerSearch($query, $this->transformer, "default", $requester);
    }

    /**
     * @param $data
     * @param null $requester
     * @return \Devesharp\CRUD\RepositoryInterface|\App\Repositories\UsersRepository
     */
    protected function makeSearch(&$data, $requester = null)
    {
        /** @var \App\Repositories\UsersRepository $query */
        $query = parent::makeSearch($data, $requester);

        if(isset($data['filters.noGetMe']) && !empty($data['filters.noGetMe'])) {
            $query->whereNotInt('id', $requester->id);
        }

        return $query;
    }

    /**
     * @param $id
     * @param $requester
     * @return bool
     * @throws \Devesharp\CRUD\Exception
     */
    public function delete($id, $requester = null)
    {
        try {
            DB::beginTransaction();

            $model = $this->repository->findIdOrFail($id);

            // Authorization
            $this->policy->delete($requester, $model);

            $this->repository->updateById($id, ["enabled" => false]);
            //            $this->repository->deleteById($id, $requester);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
