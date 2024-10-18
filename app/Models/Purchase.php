<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory; 
    protected $table = 'purchase_material';
    protected $fillable = ['purchase_date','supplier_id ','transportation_amount','grand_total','total_paid',     '	remaining_amount'];
} 
