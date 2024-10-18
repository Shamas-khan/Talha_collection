<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Employee;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class payrollController extends Controller
{
    public function salarydetaillist(Request $request,string $date)
    {
        
        $draw = $request->input('draw');

        $query = DB::table('employee_monthly_salary')
            ->leftJoin('employees', 'employee_monthly_salary.employee_id', '=', 'employees.employee_id')
           
            ->select(
                'employee_monthly_salary.id',
                'employee_monthly_salary.employee_id',
                'employee_monthly_salary.basic_salary',
                'employee_monthly_salary.days_worked',
                'employee_monthly_salary.late_comming',
                'employee_monthly_salary.early_going',
                'employee_monthly_salary.days_leaves',
                'employee_monthly_salary.days_absents',
                'employee_monthly_salary.days_holidays',
                'employee_monthly_salary.total_hours',
                'employee_monthly_salary.deductions',
                'employee_monthly_salary.allowances',
                'employee_monthly_salary.gross_salary',
                'employee_monthly_salary.net_salary',
                
                'employees.name as e_name',
                'employees.fname as f_name'
            )->where('employee_monthly_salary.salary_month',$date);
        $searchValue = $request->input('search.value');
            if ($searchValue) {
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('employees.name', 'like', "%$searchValue%")
                            ->orWhere('employee_monthly_salary.basic_salary', 'like', "%$searchValue%");
                            
                });
            }
        $totalRecords = $query->count();
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);
        $query->orderBy('employee_monthly_salary.id', 'desc');
        $customers = $query->get();
        $data = [
                    'draw' => (int)$draw,
                    'recordsTotal' => $totalRecords, 
                    'recordsFiltered' => $totalRecords, 
                    'data' => $customers, 
                ];
        return response()->json($data);
       
       
        
    }
    
    public function generateSalaries(Request $request)
{
    // Input validation
    $validatedData = $request->validate([
        'attendance_month' => 'required|date_format:Y-m',
    ]);

    $attendanceDatee = $request->input('attendance_month');
    $attendanceDate = Carbon::parse($request->input('attendance_month'));

    // Check if attendance exists for the given month
    $attendanceNotFound = DB::table('employee_attendance')
                            ->whereMonth('attendance_date', $attendanceDate->month)
                            ->whereYear('attendance_date', $attendanceDate->year)
                            ->doesntExist();
    
    if ($attendanceNotFound) {
        return redirect()->route('payroll.index')->with('error', "Attendance not found for {$attendanceDate->format('F Y')}");
    }

    // Check if salary for the given month already exists
    $salaryExists = DB::table('employee_monthly_salary')
                      ->where('salary_month', $attendanceDate->format('Y-m'))
                      ->exists();
    
    if ($salaryExists) {
        return redirect()->route('payroll.index')->with('error', "Salary for the month of {$attendanceDate->format('F Y')} already exists.");
    }

    // Fetch all employees
    $employees = DB::table('employees')->get();

    $salaryDetails = [];

    foreach ($employees as $employee) {
        // Fetch employee attendance for the given month
        $attendances = DB::table('employee_attendance')
                        ->where('employee_id', $employee->employee_id)
                        ->whereMonth('attendance_date', $attendanceDate->month)
                        ->whereYear('attendance_date', $attendanceDate->year)
                        ->get();

        // Initialize attendance variables
        $totalWorkDays = 0;
        $totalWorkHours = 0;
        $totalLateDays = 0;
        $totalEarlyDays = 0;
        $totalHolidays =   DB::table('employee_attendance')
                            ->where('is_holiday', true)
                            ->whereMonth('attendance_date', Carbon::parse($attendanceDate)->month)
                            ->whereYear('attendance_date', Carbon::parse($attendanceDate)->year)
                            ->count();
        $totalAbsent = 0;
        $totalLeaves = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->status == 'present') {
                $totalWorkDays++;

                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);

                // Expected Check-In and Check-Out times
                $expectedCheckInTime = Carbon::parse('09:30:00');
                $expectedCheckOutTime = Carbon::parse('20:30:00');

                // Late arrival check
                if ($checkIn->greaterThan($expectedCheckInTime)) {
                    $totalLateDays++;
                }

                // Early departure check
                if ($checkOut->lessThan($expectedCheckOutTime)) {
                    $totalEarlyDays++;
                }

                $workHours = $checkOut->diffInHours($checkIn);
                $totalWorkHours += $workHours;
            } elseif ($attendance->status == 'absent') {
                $totalAbsent++;
            } elseif ($attendance->status == 'leave') {
                $totalLeaves++;
            }
        }

        // Calculate salary deductions for late arrivals and early departures
        $lateDayDeductions = intdiv($totalLateDays, 2); // 1 day deduction for every 2 late days
        $earlyDayDeductions = intdiv($totalEarlyDays, 2); // 1 day deduction for every 2 early departures

        // Salary calculation
        $basicSalary = $employee->basicsalary;
        $grossSalary = $basicSalary;
        $deduction = ($basicSalary / 30) * ($totalAbsent + $lateDayDeductions + $earlyDayDeductions); // Calculate deduction
        $allowances = 0; // Calculate allowances as per your logic
        $netSalary = $grossSalary - $deduction + $allowances;

        // Store salary details
        $salaryDetails[] = [
            'employee_id' => $employee->employee_id,
            'name' => $employee->name,
            'fname' => $employee->fname,
            'basic_salary' => number_format($basicSalary, 2),
            'work_days' => $totalWorkDays,
            'work_hours' => $totalWorkHours,
            'holidays' => $totalHolidays,
            'absent' => $totalAbsent,
            'leaves' => $totalLeaves,
            'late_days' => $totalLateDays,
            'early_days' => $totalEarlyDays,
            'deduction' => number_format($deduction, 2),
            'allowances' => number_format($allowances, 2),
            'gross_salary' => number_format($grossSalary, 2),
            'net_salary' =>$netSalary,
        ];
    }

    // Return the view with salary details
    return view('hr.payroll.generate', compact('salaryDetails', 'attendanceDate','attendanceDatee', 'totalHolidays'));
}

    public function salarydetail(string $date)
    {
       
       
        return view('hr.payroll.salarydetail',compact('date'));
    }
    public function index()
    {
       
       
        return view('hr.payroll.index');
    }
    public function salarylisting(Request $request)
    {
        $draw = $request->input('draw'); 
        $query = DB::table('employee_monthly_salary')
                        ->select('salary_month', 
                                DB::raw('SUM(basic_salary) as total_basic_salary'),
                                DB::raw('SUM(gross_salary) as total_gross_salary'),
                                DB::raw('SUM(net_salary) as total_net_salary'))
                        ->groupBy('salary_month')
                        ->orderByDesc('salary_month')
                        ;
         $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->where(function ($query) use ($searchValue) {
                                $query->where('name', 'like', "%$searchValue%");
                              
                            });
                        }

                        $totalRecords = $query->count();

                        $start = $request->input('start', 0);
                        $length = $request->input('length', 10);
                        $query->offset($start)->limit($length);
                        $salary = $query->get();
                $data = [
                            'draw' => (int)$draw,
                            'recordsTotal' => $totalRecords, 
                            'recordsFiltered' => $totalRecords, 
                            'data' => $salary,
                        ];
                     
                       
                        return response()->json($data);
        
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
        // Validate the request data
        $validated = $request->validate([
            'days_holidays' => 'required', 
            'salary_month' => 'required', 
            'employe_id.*' => 'required', // Validate employee IDs
            'work_days.*' => 'required',
            'basic_salary.*' => 'required',
            'late_days.*' => 'required',
            'early_days.*' => 'required',
            'absent.*' => 'required',
            'leaves.*' => 'required',
            'deduction.*' => 'required', 
            'allowances.*' => 'nullable', 
            'gross_salary.*' => 'required', 
            'net_salary.*' => 'required', 
        ]);
    
        $salary_month = $request->input('salary_month');
        $days_holidays = $request->input('days_holidays');
    
        // Check if salary for the given month already exists
        $exists = DB::table('employee_monthly_salary')
            ->where('salary_month', $salary_month)
            ->exists();
        if ($exists) {
            return redirect()->route('payroll.index')->with('error', "Salary for month {$salary_month} already exists.");
        }
    
        // Prepare data for insertion
        $data = [];
        $ledger = [];
        foreach ($request->input('employe_id') as $index => $employeeId) {
            // Clean and format the fields
            $basic_salary = str_replace(',', '', $request->input('basic_salary')[$index]);
            $deduction = str_replace(',', '', $request->input('deduction')[$index]);
            $allowances = str_replace(',', '', $request->input('allowances')[$index]);
            $gross_salary = str_replace(',', '', $request->input('gross_salary')[$index]);
            $net_salary = str_replace(',', '', $request->input('net_salary')[$index]);
    
            // Add salary data to the array
            $data[] = [
                'employee_id' => $employeeId,
                'days_worked' => $request->input('work_days')[$index],
                'late_comming' => $request->input('late_days')[$index],
                'early_going' => $request->input('early_days')[$index],
                'days_absents' => $request->input('absent')[$index],
                'days_leaves' => $request->input('leaves')[$index],
                'deductions' => $deduction,
                'allowances' => $allowances,
                'gross_salary' => $gross_salary + $allowances,
                'net_salary' => $net_salary + $allowances,
                'basic_salary' => $basic_salary,
                'salary_month' => $salary_month,
                'days_holidays' => $days_holidays,
            ];
        }
    
        // Insert data and get the last inserted ID for each employee
        try {
            foreach ($data as $salaryData) {
                $salaryId = DB::table('employee_monthly_salary')->insertGetId($salaryData);
    
                // Insert the ledger entry
                $ledger[] = [
                    'employee_id' => $salaryData['employee_id'],
                    'transaction_date' => now(),
                    'status' => 'Salary',
                    'description' => 'Salary of '. $salaryData['salary_month'],
                    'credit' => $salaryData['net_salary'],
                    'employee_monthly_salary_id' => $salaryId,
                ];

                DB::table('employees')
                ->where('employee_id', $salaryData['employee_id'])
                ->increment('remaining_amount', $salaryData['net_salary']);
            }
    
            // Insert ledger entries
            DB::table('employee_ledger')->insert($ledger);
    
            return redirect()->route('payroll.index')->with('success', 'Salaries calculated and saved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payroll.index')->with('error', 'Cannot generate salaries: ' . $e->getMessage());
        }
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
