<?php

namespace App\Http\Controllers;
use App\Models\FinishProduct;
use Illuminate\Http\Request;
use DB;

class ForecastingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fproduct=FinishProduct::all();
        return view('forecasting.index',compact('fproduct'));
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
    // public function forecastProduction(Request $request ,string $id)
    // {
    //     $id=$request->get('id');
    //     $rawMaterial = DB::table('product_materials')
    //         ->where('product_materials.finish_product_id', $id)
    //         ->select('material_qty,raw_material_id')
    //         ->first();
    //     foreach($rawMaterial as $raw){

    //     }

    // }

public function getforcasting(Request $request)
{
    $id=$request->post('fpro');
    // Step 1: Aggregate raw material requirements for each finished product
    $materialRequirements = DB::table('product_materials')
    
        ->select('finish_product_id', 'raw_material_id', DB::raw('SUM(material_qty) as total_material_qty'))
        ->where('product_materials.finish_product_id', $id)
        ->groupBy('finish_product_id', 'raw_material_id')
        ->get();

    // Step 2: Join with raw_stock to get available quantities
    $materialRequirements = $materialRequirements->map(function($requirement) {
        $stock = DB::table('raw_stock')
            ->where('raw_material_id', $requirement->raw_material_id)
            ->first();

        if ($stock) {
            $requirement->available_quantity = $stock->available_quantity;
        } else {
            $requirement->available_quantity = 0;
        }

        // Calculate possible production for each raw material
        $requirement->possible_production = floor($requirement->available_quantity / $requirement->total_material_qty);

        return $requirement;
    });

    // Step 3: Find the limiting raw material for each product
    $productionCapacity = $materialRequirements->groupBy('finish_product_id')->map(function($requirements) {
        return $requirements->min('possible_production');
    });
    foreach($productionCapacity as $pro){
        echo "<pre>";
        print_r($pro);
    }
    die;
    print_r($productionCapacity);die;
    dd($productionCapacity);

    return response()->json($productionCapacity);
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
