<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class issueMaterial extends Model
{
    protected $primaryKey = 'issue_material_id';
    protected $table = 'issue_material';
    public $timestamps = false;
    use HasFactory;
}
