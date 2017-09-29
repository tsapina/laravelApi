<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Role;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname','lastname', 'email', 'password', 'adress'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'permission_role_user', 'user_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Permission', 'permission_role_user', 'user_id', 'permission_id');
    }

    public function permissionsRoles(){
        return $this->hasMany('App\PermissionRoleUser', 'user_id');
    }
   

}
 