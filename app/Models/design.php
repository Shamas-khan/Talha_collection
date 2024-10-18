<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class design extends Model
{
    protected $table = 'design';
    protected $primaryKey = 'design_id';
    protected $fillable = ['design_code','name', 'unit_id','cost','img'];
    public $timestamps = false;
    use HasFactory;
}
