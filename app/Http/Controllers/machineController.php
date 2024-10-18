<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Http\Requests\MachineRequest;

class machineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setup.machine');
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
    public function store(MachineRequest $request)
    {
       
       $machine = Machine::create($request->validated());
      
       session()->flash('success', 'Machine added successfully!');
       return redirect('machine');
    }
    public function list(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder with default sorting to show the latest record first
        $query = Machine::query();
    
        // Apply sorting if specified in the request
        $sortColumnIndex = $request->input('order.0.column');
        // $sortDirection = $request->input('order.0.dir', 'desc'); // Default to 'asc' if no direction is specified
        $sortColumnName = $request->input("columns.$sortColumnIndex.data");
        
        if ($sortColumnName) {
           
            $query->orderBy('karai_machine_id', 'desc');
        } else {
           
            $query->orderBy('karai_machine_id', 'asc');
        }
    
        // Apply search functionality based on area_code
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where('area_code', 'like', '%' . $searchValue . '%');
        }

        // Get total records before applying pagination
        $totalRecords = $query->count();
        
        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);
    
        // Fetch the data
        $machines = $query->get();
    
        // Prepare the response data
        $data = [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
            'data' => $machines, // Data to be displayed in DataTables
        ];
    
        // Return the response as JSON
        return response()->json($data);
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
