<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermissionRoleUser extends Model
{
    protected $fillable = [
        'role_id','permission_id','user_id'
    ];

    protected $table = 'permission_role_user';
    //
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function permission()
    {
        return $this->belongsTo('App\Permission', 'permission_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }
}
