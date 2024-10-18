<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    use HasFactory;

    
    protected $table = 'employee_attendance';

   
    protected $primaryKey = 'id';

    
    protected $fillable = [
        'employee_id',
        'check_in',
        'check_out',
    ];

    
    public $timestamps = false;

    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
