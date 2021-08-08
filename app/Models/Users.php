<?php

namespace App\Models;

use App\Presenters\UsersPresenter;
use App\Services\AuthService;
use App\Services\UsersPermissionsService;
use Devesharp\CRUD\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Users extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, PresentableTrait;

    protected $presenter = UsersPresenter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        "email_verified_at" => "datetime",
        "deleted_at" => "datetime",
    ];


    /**
     * @param string|array $permissionCheck
     * @return bool
     */
    function hasPermission(string | array $permissionCheck): bool {
        return app(UsersPermissionsService::class)->hasPermission($this, $permissionCheck);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            't' => app(AuthService::class)->createTokenForUser($this)
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(UsersPermissions::class, 'user_id', 'id');
    }
}
