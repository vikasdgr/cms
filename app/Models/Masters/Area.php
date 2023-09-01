<?php

namespace App\Models\Masters;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Area extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "areas";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'department_id',
        'area_user_id',
        'person_contact_no',
    ];

    public function area_user()
    {
        return $this->belongsTo(User::class,'area_user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class,'department_id');
    }


}
