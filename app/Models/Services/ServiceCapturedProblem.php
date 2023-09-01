<?php

namespace App\Models\Services;

use App\Models\Masters\Problem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ServiceCapturedProblem extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "service_captured_problems";
    protected $connection = 'mysql';
    protected $fillable = [
        'case_id',
        'service_id',
        'problem_id',
        'is_remarks',
        'remarks',
        'problem_status',
    ];

    public function problem(){
        return $this->belongsTo(Problem::class,'problem_id');
    }
}
