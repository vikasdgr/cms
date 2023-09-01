<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Maintenance extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "maintenances";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'description',
    ];

}
