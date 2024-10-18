<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishProduct extends Model
{
    protected $table = 'finish_product';
    protected $primaryKey = 'finish_product_id';
    use HasFactory;
}
