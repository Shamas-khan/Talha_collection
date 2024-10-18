<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'bank'; 
    protected $primaryKey = 'bank_id'; 

    
    protected $fillable = [
        'bank_name',
        'account_number',
        'opening_balance',
        'branch_code',
        'running_balance',
        'created_at',
        'updated_at',
    ];

    
    public $timestamps = false;

   
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
