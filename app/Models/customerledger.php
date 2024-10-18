<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customerledger extends Model
{
    use HasFactory;
    protected $table = 'customer_ledger'; // Table ka naam
    protected $primaryKey = 'customer_ledger_id'; // Primary key
    public $timestamps = false; // Agar timestamps use nahi kar rahe

    protected $fillable = [
        'debit', 'credit', 'customer_id', 'sell_id', 
        'paymentvoucher_id', 'customer_payment_id', 
        'created_at', 'status', 'narration'
    ];
}


