<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class MachineType extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "machine_types";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
    ];

    public function models()
    {
        return $this->hasMany(MachineModel::class,'machine_type_id');
    }

}
