<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class karahi extends Model
{
    protected $table = 'karai_vendor';
    protected $primaryKey = 'karai_vendor_id';
    protected $fillable = ['name','company' ,'contact','address','total_amount','remaining_amount','op_balance'];
    public $timestamps = false;
    use HasFactory; 
}
