<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class issuekarahi extends Model
{
    protected $table = 'issue_karahi_material';
    protected $primaryKey = 'issue_karahi_material_id';
    protected $fillable = ['karai_vendor_id','raw_material_id', 'issue_qty','available_qty'];
    public $timestamps = false;
    use HasFactory;
}
