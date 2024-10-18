<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    protected $fillable = [
        'name', 
        'fname', 
        'contact', 
        'address', 
        'basicsalary',
        'remaining_amount'
    ];
    public $timestamps = false;
    use HasFactory;
}
