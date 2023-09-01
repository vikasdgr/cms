<?php

namespace App\Models\Machine;

use App\Models\Auth\User;
use App\Models\Masters\Brand;
use App\Models\Masters\MachineModel;
use App\Models\Masters\MachineType;
use App\Models\Masters\Department;
use App\Models\Masters\Area;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Machine extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "machines";
    protected $connection = 'mysql';
    protected $fillable = [
        'name_code_no',
        'serial_no',
        'brand_id',
        'model_id',
        'machine_type_id',
        'department_id',
        'area_id',
        'buy_date',
        'installation_date',
        'warranty_upto_date',
    ];


    public function getBuyDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setBuyDateAttribute($date)
    {
        $this->attributes['buy_date'] = setDateAttribute($date);
    }

    public function getInstallationDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setInstallationDateAttribute($date)
    {
        $this->attributes['installation_date'] = setDateAttribute($date);
    }
    public function getWarrantyUptoDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setWarrantyUptoDateAttribute($date)
    {
        $this->attributes['warranty_upto_date'] = setDateAttribute($date);
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function machine_model(){
        return $this->belongsTo(MachineModel::class,'model_id');
    }
    public function machine_type(){
        return $this->belongsTo(MachineType::class,'machine_type_id');
    }

    public function department(){
        return $this->belongsTo(Department::class,'department_id');
    }

    public function area(){
        return $this->belongsTo(Area::class,'area_id');
    }
}
