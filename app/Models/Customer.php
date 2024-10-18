<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory; 
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    protected $fillable = ['name','company' ,'contact','address','total_amount','remaining_amount','op_balance'];
    
} 
 