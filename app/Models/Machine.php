<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'karai_machine';
    protected $primaryKey = 'karai_machine_id';
    protected $fillable = [ 'area_code','head_code','size'];
    public $timestamps = false;
    use HasFactory;
}
