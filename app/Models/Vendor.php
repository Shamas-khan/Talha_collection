<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory; 
    protected $table = 'vendor';
    protected $primaryKey = 'vendor_id';
    protected $fillable = ['name','contact','cnic','address','total_amount','remaining_amount','op_balance'];
}
