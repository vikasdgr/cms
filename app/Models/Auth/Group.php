<?php

namespace App\Models\Auth;

use App\Traits;
use App\Models\Auth\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = 'groups';
    protected $fillable = ['group_name', 'description'];

    public function permissions() {
      return $this->belongsToMany(Permission::class, 'group_permissions', 'group_id', 'permission_id')->withTimestamps()
          ->select(['permissions.id', 'permissions.name', 'permissions.label']);
    }
}
