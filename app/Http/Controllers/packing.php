<?php

namespace App\Http\Controllers;
use App\Models\FinishProduct;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use App\Http\Requests\packingreq;
use DB;

class packing extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fproduct=FinishProduct::all();
        $smallshoper = RawMaterial::where('type', 3)->get();
        $bigshoper = RawMaterial::where('type', 4)->get();
        return view('setup.packing',compact('fproduct','smallshoper','bigshoper'));
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
    public function store(packingreq $request)
    {
        $validatedData = $request->validated();
        $finish_product_id = $validatedData['finish_product_id'];
        $small_shoper_id = $validatedData['small_shoper_id'];
        $small_product_qty = $validatedData['small_product_qty'];
        $big_shoper_id = $validatedData['big_shoper_id'];
        $big_product_qty = $validatedData['big_product_qty'];


        DB::table('finish_product')
                        ->where('finish_product_id', $finish_product_id)
                        ->update([
                            'small_shoper_id' => $small_shoper_id,
                            'small_product_qty' => $small_product_qty,
                            'big_shoper_id' => $big_shoper_id,
                            'big_product_qty' => $big_product_qty,
                        ]);

        

        session()->flash('success', 'packing added successfully!');
       return redirect('packing');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function list(Request $request)
    {
        try {
            // Get the draw parameter
            $draw = $request->input('draw');
    
            // Initialize the query builder with default sorting to show the latest record first
            $query = DB::table('finish_product')
                ->leftJoin('raw_material as small_shoper', 'finish_product.small_shoper_id', '=', 'small_shoper.raw_material_id')
                ->leftJoin('raw_material as big_shoper', 'finish_product.big_shoper_id', '=', 'big_shoper.raw_material_id')
                ->select(
                    'finish_product.product_name',
                    'finish_product.small_product_qty',
                    'finish_product.big_product_qty',
                    'small_shoper.name as small_shoper_name',
                    'big_shoper.name as big_shoper_name'
                );
    
            // Apply sorting if specified in the request
            $query->orderBy('finish_product.finish_product_id', 'desc');
    
            // Apply search functionality based on product_name
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->where('finish_product.product_name', 'like', '%' . $searchValue . '%');
            }
    
            // Get total records before applying pagination
            $totalRecords = DB::table('finish_product')->count();
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
                'data' => $designs
            ];
    
            // Return the response as JSON
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => (int)$request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
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
