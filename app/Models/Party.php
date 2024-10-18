<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;
    protected $table = 'parties';
    protected $primaryKey = 'parties_id ';
    protected $fillable = ['name', 'phone_number', 'opening_balance','total_amount','remaining_amount','paid_amount'];
}
