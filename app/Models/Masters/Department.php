<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Department extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "departments";
    protected $connection = 'mysql';
    protected $fillable = [
        'name'
    ];

    public function areas(){
        return $this->hasMany(Area::class,'department_id');
    }

}
