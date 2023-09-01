<?php

namespace App\Models\Services;

use App\Models\Masters\Maintenance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class MachineService extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "services";
    protected $connection = 'mysql';
    protected $fillable = [
        'service_no',
        'parent_service_id',
        'case_id',
        'service_date',
        'service_time',
        'remarks',
        'technician_name',
        'status',
        'service_type',
    ];

    public function getServiceDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setServiceDateAttribute($date)
    {
        $this->attributes['service_date'] = setDateAttribute($date);
    }
    public function service_maintenances(){
        return $this->hasMany(ServiceMaintenance::class,'service_id');
    }
    public function service_problems(){
        return $this->hasMany(ServiceCapturedProblem::class,'service_id');
    }
    public function service_resolving_actions(){
        return $this->hasMany(ServiceResolvingAction::class,'service_id');
    }
    public function case(){
        return $this->belongsTo(MachineCase::class,'case_id');
    }
}
