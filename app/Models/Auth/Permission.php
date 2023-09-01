<?php

namespace App\Models\Auth;

use App\Models\Auth\Group;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = "permissions";
    protected $fillable = ['name', 'label', 'opt_group_id', 'order_no'];
    protected $connection = 'mysql';

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'opt_group_id', 'id')
            ->orderBy('order_no');
    }

    public function role()
    {
        return $this->belongsToMany(Role::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_permissions', 'permission_id', 'group_id')->withTimestamps();
    }
}
