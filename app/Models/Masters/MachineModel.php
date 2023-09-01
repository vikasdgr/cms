<?php

namespace App\Models\Masters;

use App\Models\Attachment\SharedResource;
use App\Models\Machine\Machine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class MachineModel extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "machine_models";
    protected $connection = 'mysql';
    protected $fillable = [
        'model_no',
        'machine_type_id',
        'brand_id'
    ];

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

     public function machine_type(){
        return $this->belongsTo(MachineType::class,'machine_type_id');
    }

    public function resources(){
        return $this->hasMany(SharedResource::class,'resourceable_id')->where('resourceable_type',MachineModel::class);
    }

    public function machine_image(){
        return $this->hasOne(SharedResource::class,'resourceable_id')->where('resourceable_type',MachineModel::class);
    }


}
