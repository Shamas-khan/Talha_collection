<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use DB;
use Illuminate\Support\Facades\Validator;
class AttendenceController extends Controller
{
   

  
    
    public function markAttendance(Request $request)
    {
        $errors = [];
    
        if ($request->has('is_holiday')) {
            $validatedData = $request->validate([
                'attendance_date' => 'required|date',
            ], [
                'attendance_date.required' => 'Attendance date is required.',
            ]);
    
            $existingAttendance = DB::table('employee_attendance')
                ->whereDate('attendance_date', $request->input('attendance_date'))
                ->first();
    
            if ($existingAttendance) {
                return redirect()->back()->with('error', 'Attendance already marked');
                $errors[] = "Attendance already marked today {$request->input('attendance_date')}.";
            } else {
                DB::table('employee_attendance')->insert([
                    'attendance_date' => $validatedData['attendance_date'],
                    'is_holiday' => true,
                ]);
            }
        } else {
            $validatedData = $request->validate([
                'attendance_date' => 'required|date',
                'employee_id.*' => 'required',
                'status.*' => 'required|in:present,leave,absent',
                'check_in.*' => 'nullable|required_if:status.*,present|date_format:H:i',
                'check_out.*' => 'nullable|required_if:status.*,present|date_format:H:i|after_or_equal:check_in.*',
            ], [
                'attendance_date.required' => 'Attendance date is required.',
                'check_in.*.required_if' => 'Check-in time is required when status is present.',
                'check_out.*.required_if' => 'Check-out time is required when status is present.',
                'check_out.*.after_or_equal' => 'Check-out time must be after or equal to check-in time.',
                'status.*.required' => 'Please select a status for each employee.',
            ]);
    
            $attendanceDate = $request->input('attendance_date');
            $isHoliday = $request->has('is_holiday') ? 1 : 0;
            $employeeIds = $request->input('employee_id', []);
            $statuses = $request->input('status', []);
            $checkIns = $request->input('check_in', []);
            $checkOuts = $request->input('check_out', []);

            $existingAttendance = DB::table('employee_attendance')
                    ->whereDate('attendance_date', $attendanceDate)
                    ->first();

            if ($existingAttendance) {
                    $errors[] = "Today Attendance already marked  {$attendanceDate}.";
                    }

            foreach ($employeeIds as $index => $employeeId) {
                
                $data = [
                    'employee_id' => $employeeId,
                    'attendance_date' => $attendanceDate,
                    'status' => $statuses[$index] ?? null,
                    'check_in' => ($statuses[$index] === 'present') ? ($checkIns[$index] ?? null) : null,
                    'check_out' => ($statuses[$index] === 'present') ? ($checkOuts[$index] ?? null) : null,
                    'is_holiday' => $isHoliday,
                ];
    
                // Validate data for each employee
                $validator = Validator::make($data, [
                    'employee_id' => 'required|exists:employees,employee_id',
                    'status' => 'required|in:present,leave,absent',
                    'check_in' => ($data['status'] === 'present') ? 'nullable|date_format:H:i' : 'nullable',
                    'check_out' => ($data['status'] === 'present') ? 'nullable|date_format:H:i|after_or_equal:check_in' : 'nullable',
                    'is_holiday' => 'required|boolean',
                ]);
    
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->first();
                    continue;
                }else{
                    DB::table('employee_attendance')->insert($data);
                }
    
                
               
            }
        }
    
        if (!empty($errors)) {
            return redirect()->back()->with('error', implode(', ', $errors));
        }
    
        return redirect()->back()->with('success', 'Attendance marked successfully');
    }
    
    
    public function index()
    {
        $employees = Employee::all();
        return view('hr.attendence.add', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
