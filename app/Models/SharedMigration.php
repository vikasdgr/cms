<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedMigration extends Model
{
    use HasFactory;
    protected $table = 'migrations';
    protected $connection = 'mysql';

}
