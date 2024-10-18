<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use App\Http\Resources\RawMaterialResource;
use App\Http\Requests\StoreRawMaterialRequest;
use DB;

class RawMaterialController extends Controller
{
    
    public function index()
    {
        $units=Unit::all();
        $data=RawMaterialResource::collection(Unit::all());
        return view('raw_material.index',compact('data','units'));
    }
    public function  getAvailableQtyOfRawMaterial(Request $request){
        $id=$request->input("id");
        $raw = DB::table('raw_stock')
        ->where('raw_material_id', $id)
        ->select('available_quantity')
        ->first();
        echo $raw->available_quantity;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function listing(Request $request)
    {
       
        $draw = $request->input('draw');
        $query = DB::table('raw_material')
            ->leftJoin('unit', 'raw_material.unit_id', '=', 'unit.unit_id')
            ->leftJoin('raw_stock', 'raw_material.raw_material_id', '=', 'raw_stock.raw_material_id')
            ->select(
                'raw_material.raw_material_id',
                'raw_material.name',
                'unit.name as unit_name',
                'raw_stock.available_quantity'
            );
        $searchValue = $request->input('search.value');
            if ($searchValue) {
                    $query->where(function ($query) use ($searchValue) {
                        $query->where('raw_material.name', 'like', "%$searchValue%")
                            ->orWhere('unit.name', 'like', "%$searchValue%")
                            ->orWhere('raw_stock.available_quantity', 'like', "%$searchValue%");
                });
            }
        $totalRecords = $query->count();
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);
        $query->orderBy('raw_material_id', 'desc');
        $customers = $query->get();
        $data = [
                    'draw' => (int)$draw,
                    'recordsTotal' => $totalRecords, 
                    'recordsFiltered' => $totalRecords, 
                    'data' => $customers, 
                ];
        return response()->json($data);
    }
    
    
    public function store(StoreRawMaterialRequest $request)
    {
        $Raw = RawMaterial::create($request->validated());
        session()->flash('success', 'Raw Material created successfully!');
        return redirect('raw_material');
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
