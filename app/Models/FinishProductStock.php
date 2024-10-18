<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishProductStock extends Model
{
    protected $primaryKey = 'finish_product_stock_id';
    protected $table = 'finish_product_stock';
    protected $fillable = ['finish_product_id', 'quantity'];
    public $timestamps = false;
    use HasFactory;
}
