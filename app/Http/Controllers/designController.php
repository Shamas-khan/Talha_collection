<?php

namespace App\Http\Controllers;
use App\Models\design;
use App\Models\Unit;
use App\Http\Requests\designRequest;
use Illuminate\Http\Request;
use DB;
class designController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Unit = Unit::all();
       return view('setup.design',compact('Unit'));
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
    public function store(designRequest $request)
    {
        
        $design = design::create($request->validated());
        if ($request->hasFile('img')) {
            $imageName = time().'.'.$request->img->extension();  
            $request->img->move(public_path('images'), $imageName);
            $design->img = $imageName;
            $design->save();
        }
        

        DB::table('raw_material')->insert([
            'name' => $design->name, 
            'unit_id' => $design->unit_id,
            'type' => $request->type 
        ]);


        session()->flash('success', 'Design added successfully!');
        return redirect('design');
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
    public function list(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder with default sorting to show the latest record first
        $query = DB::table('design')
            ->leftJoin('unit', 'design.unit_id', '=', 'unit.unit_id')
            ->select(
                'design.design_code',
                'design.name',
                // 'design.cost',
                'design.img',
                'unit.name as unit_name',
                'design.design_id'
            );
    
        // Apply sorting if specified in the request
        $sortColumnIndex = $request->input('order.0.column');
        $sortColumnName = $request->input("columns.$sortColumnIndex.data");
    
        if ($sortColumnName) {
            $query->orderBy($sortColumnName, $request->input('order.0.dir', 'asc'));
        } else {
            $query->orderBy('design.design_id', 'desc');
        }
    
        // Apply search functionality based on area_code
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where('design.name', 'like', '%' . $searchValue . '%');
        }
    
        // Get total records before applying pagination
        $totalRecords = DB::table('design')->count();
        $filteredRecords = $query->count();
    
        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);
    
        // Fetch the data
        $designs = $query->get();
    
        // Prepare the response data
        $data = [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $filteredRecords, // Total records after filtering
            'data' => $designs->map(function ($design) {
                return [
                    'name' => $design->name,
                    'design_code' => $design->design_code,
                    'unit_name' => $design->unit_name,
                    // 'cost' => $design->cost,
                    'img' => $design->img ? asset('images/' . $design->img) : null,
                ];
            }),
        ];
    
        // Return the response as JSON
        return response()->json($data);
    }
    
    
}
