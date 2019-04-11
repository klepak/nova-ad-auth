<?php

namespace Klepak\NovaAdAuth\Models;


class ApiUser extends User
{
    protected $guard_name = 'api';
    protected $table = "users";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
