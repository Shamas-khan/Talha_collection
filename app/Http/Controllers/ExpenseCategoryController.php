<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Http\Requests\ExpenseCategoryRequest;


class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setup.expensecateory');
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
    public function store(ExpenseCategoryRequest $request)
    {
        $Raw = ExpenseCategory::create($request->validated());
        session()->flash('success', 'expense Category created successfully!');
        return redirect('expensecategory');
    }

    public function listing(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder
        $query = ExpenseCategory::query();
    
        $query->orderBy('expense_category.expense_category_id', 'desc');
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
        $customers = $query->get();
    
        // Prepare the response data
        $data = [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
            'data' => $customers, // Data to be displayed in DataTables
        ];
     
        // Return the response as JSON
        return response()->json($data);
    }
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
