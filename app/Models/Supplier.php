<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'supplier_id';
    protected $fillable = ['name','company' ,'contact','address','total_amount','remaining_amount','op_balance'];
    use HasFactory;
}
 