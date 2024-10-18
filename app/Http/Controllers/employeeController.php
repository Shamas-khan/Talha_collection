<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use DB;
class employeeController extends Controller
{


    public function ledgerlist(Request $request, string $id)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value');
        $page = $start / $length;
    
       
        $query = "
            WITH RunningBalance AS (
                SELECT
                    bl.employee_ledger_id,
                    bl.debit,
                    bl.credit,
                    bl.employee_id,
                    bl.employee_monthly_salary_id,
                    bl.paymentvoucher_id,
                    bl.transaction_date as created_at,
                    bl.status,
                    bl.description as narration,
                    @balance := @balance + bl.credit - bl.debit AS running_balance,
                     CASE 
                            WHEN bl.credit > 0 THEN 'Cr'
                            WHEN bl.debit > 0 THEN 'Dr'
                            ELSE ''
                        END AS balance_type
                    
                FROM
                    (SELECT @balance := ?) AS var_init,
                    employee_ledger AS bl
                WHERE
                    bl.employee_id = ?
                    " . ($searchValue ? "AND (bl.description LIKE ? OR DATE(bl.transaction_date) LIKE ?)" : "") . "
                ORDER BY
                    bl.employee_ledger_id ASC
            )
            SELECT *
            FROM RunningBalance
            ORDER BY employee_ledger_id DESC
            LIMIT ?, ?
        ";
    
       
        $bindings = [
            $initialBalance = 0,
            $id,
        ];
    
        if ($searchValue) {
            $bindings[] = '%' . $searchValue . '%';
            $bindings[] = '%' . $searchValue . '%';
        }
    
        $bindings[] = (int) $start;
        $bindings[] = (int) $length;
    
        // Execute the query
        $results = DB::select($query, $bindings);
    
        // Calculate total records
        $totalRecords = DB::table('employee_ledger')
            ->where('employee_id', $id)
            ->when($searchValue, function ($query, $searchValue) {
                return $query->where(function ($query) use ($searchValue) {
                    $query->where('description', 'like', "%$searchValue%")
                        ->orWhere(DB::raw('DATE(transaction_date)'), 'like', "%$searchValue%");
                });
            })
            ->count();

            foreach ($results as &$result) {
                $result->running_balance .= ' ' . $result->balance_type;
            }
    
        // Prepare the response data
        $data = [
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $results,
        ];
    
        // Return JSON response
        return response()->json($data);
    }
    public function listing(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder
        $query = Employee::query();
    
        // Apply sorting
        $query->orderBy('employee_id', 'desc');
        
    
        // Apply searching
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%");
                // Add more search conditions for other columns if needed
            });
        }
    
        // Get total records before applying pagination
        $totalRecords = $query->count();
    
        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);
    
        // Fetch the data
        $Employees = $query->get();
    
        // Prepare the response data
        $data = [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords, 
            'recordsFiltered' => $totalRecords, 
              'data' => $Employees->map(function ($Employee) {
                return [
                    'employee_id' => $Employee->employee_id,
                    'name' => $Employee->name,
                    'fname' => $Employee->fname,
                    'contact' => $Employee->contact,
                    'created_at' => $Employee->created_at,
                    
                    'address' => $Employee->address,
                    'basicsalary' => number_format($Employee->basicsalary, 2),
                    'remaining_amount' => number_format($Employee->total_amount, 2)
                    
                    
                ];
            })
        ];
     
        // Return the response as JSON
        return response()->json($data);
    }
    public function index()
    {
        return view('hr.employee.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hr.employee.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'fname' => 'required',
            'contact' => 'required|numeric',
           
            'address' => 'required',
            'basicsalary' => 'required',
         
        ], [
            'name.required' => 'name field is required.',
            'fname.required' => 'Father Name is required.',
            'contact.required' => 'Contact field is required.',
            'amount.numeric' => 'Contact must be a numeric value.',
           
            'address.required' => 'Address field is required',
            'basicsalary.required' => 'Basic Salary field is required.',
            
        ]);

        $cleanSalary = str_replace(',', '', $validatedData['basicsalary']);

        Employee::create([
            'name' => $validatedData['name'],
            'fname' => $validatedData['fname'],
            'contact' => $validatedData['contact'],
            'address' => $validatedData['address'],
            'basicsalary' =>  $cleanSalary,
            'remaining_amount' => 0 
        ]);

        return redirect()->route('employee.index')->with('success', 'Employee added successfully.');
   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function ledger(string $id)
    {
        $employees = DB::table('employees')
        ->where('employee_id', $id)
        ->first();

        return view('hr.employee.ledger',compact('employees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = DB::table('employees')
        ->where('employee_id', $id)
        ->first();
        return view('hr.employee.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'fname' => 'required',
            'contact' => 'required|numeric',
            'address' => 'required',
            'basicsalary' => 'required',
        ], [
            'name.required' => 'Name field is required.',
            'fname.required' => 'Father Name is required.',
            'contact.required' => 'Contact field is required.',
            'contact.numeric' => 'Contact must be a numeric value.',
            'address.required' => 'Address field is required',
            'basicsalary.required' => 'Basic Salary field is required.',
        ]);
    
        $cleanSalary = str_replace(',', '', $validatedData['basicsalary']);
    
        $employee = Employee::findOrFail($id);
        $employee->update([
            'name' => $validatedData['name'],
            'fname' => $validatedData['fname'],
            'contact' => $validatedData['contact'],
            'address' => $validatedData['address'],
            'basicsalary' => $cleanSalary,
        ]);
    
        return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
