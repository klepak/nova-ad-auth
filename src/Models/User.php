<?php

namespace Klepak\NovaAdAuth\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Adldap\Laravel\Traits\HasLdapUser;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable, HasLdapUser, HasRoles;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            if(!isset($model->attributes["api_token"]))
                $model->attributes["api_token"] = Str::random(60);
        });
    }

    public function apiUser()
    {
        return $this->hasOne(ApiUser::class, 'id', 'id');
    }

    /**
     * @return bool
     */
    public function canImpersonate()
    {
        return $this->can('impersonate users');
    }

    /**
     * @return bool
     */
    public function canBeImpersonated()
    {
        if($this->hasRole('admin'))
            return $this->id == auth()->user()->id;

        return true;
    }

    public function getAvatarPublicPath()
    {
        $avatarPath = $this->getAvatarPath();

        return $avatarPath != null ? "/storage/$avatarPath" : "/img/unknown-user.png";
    }

    public function getAvatarPath()
    {
        if(!file_exists($this->getAvatarFullPath()))
            return null;

        return $this->getAvatarRelativePath();
    }

    public function getAvatarRelativePath($id = null)
    {
        $id = $id ?? $this->id;

        return "user_thumbs/$id.png";
    }

    public function getAvatarFullPath()
    {
        return storage_path("app/public/{$this->getAvatarRelativePath()}");
    }
}
