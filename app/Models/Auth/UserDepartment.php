<?php

namespace App\Models\Auth;

use App\Traits;
use App\Models\Auth\User;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDepartment extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = 'user_departments';
    protected $fillable = ['user_id', 'department_id'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Site::class, 'department_id', 'id');
    }
}
