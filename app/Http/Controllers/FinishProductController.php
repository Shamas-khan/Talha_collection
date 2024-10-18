<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishProductRequest;
use App\Models\FinishProduct;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use DB;

class FinishProductController extends Controller
{ 
    /**
     * Display a listing of the resource.
     */
    public function reprcessproduct(Request $request)
{
    // Start a transaction
    DB::beginTransaction();
    
    try {
        // Validate the request
        $request->validate([
            'raw_material_id' => 'required',
            'quantity' => 'required',
        ]);
        $price = $this->finalPrice($request->raw_material_id, $request->quantity);
          $newperice = $price / $request->quantity;
        // Get the finished product details
        $finishedProduct = DB::table('finish_product')
            ->where('finish_product_id', $request->raw_material_id)
            ->select('product_name')
            ->first();

        if (!$finishedProduct) {
            return response()->json(['success' => false, 'message' => 'Finished product not found']);
        }

        // Check if raw material already exists
        $existingRawMaterial = DB::table('raw_material')
            ->where('name', $finishedProduct->product_name)
            ->first();

            // if ($existingRawMaterial->stock->unit_price != $price) {
            //     DB::table('raw_stock')
            //         ->where('raw_material_id', $existingRawMaterial->raw_material_id)
            //         ->update([
            //             'unit_price' => $newperice
            //         ]);
            // }

        if ($existingRawMaterial) {
            // Update the quantity in raw_stock
            DB::table('raw_stock')
                ->where('raw_material_id', $existingRawMaterial->raw_material_id)
                ->increment('available_quantity', $request->quantity);


                DB::table('raw_stock')
                        ->where('raw_material_id', $existingRawMaterial->raw_material_id)
                        ->update([
                            'unit_price' => $newperice
                        ]);
        } else {
            // Insert new data into raw_materials table
            $id = DB::table('raw_material')->insertGetId([
                'name' => $finishedProduct->product_name,
                'unit_id' => 14,
                'type' => 6,
            ]);

            // Insert data into raw_stock table
            DB::table('raw_stock')->insert([
                'raw_material_id' => $id,
                'available_quantity' => $request->quantity,
                'unit_price' => $newperice,
            ]);
        }
        $this->updateqtyForRaw($request->raw_material_id, $request->quantity);
        // Decrement the quantity in finish_product_stock
        $decrementResult = DB::table('finish_product_stock')
            ->where('finish_product_id', $request->raw_material_id)
            ->decrement('quantity', $request->quantity);

        if ($decrementResult === 0) {
            // Rollback the transaction if decrement fails
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to decrement quantity or no matching record found']);
        }

        // Commit the transaction
        DB::commit();

        return response()->json(['success' => true, 'message' => 'Data processed successfully']);
    } catch (\Exception $e) {
        // Rollback the transaction in case of any errors
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
public function finalPrice($fproductid, $qty)
{
    $total_amount = 0;
    $raw = DB::table('issue_material')
        ->where('finished_product_id', $fproductid)
        ->whereNotNull('calculation')
        ->where('calculation', '>', 0)
        ->select('calculation', 'unit_cost')
        ->get();

    foreach ($raw as $r) {
        if ($qty <= 0) {
            break; 
        }
        if ($r->calculation >= $qty) {
            $amount = $r->unit_cost * $qty;
            $total_amount += $amount;
            $qty = 0;
        } else {
            $amount = $r->unit_cost * $r->calculation;
            $total_amount += $amount;
            $qty -= $r->calculation;
        }
    }

    return $total_amount;
}



public function updateqtyForRaw($fproductid, $qty)
{
    $raw = DB::table('issue_material')
        ->where('finished_product_id', $fproductid)
        ->whereNotNull('calculation')
        ->where('calculation', '>', 0)
        ->select('issue_material.calculation as remaining_qty', 'issue_material.issue_material_id as issue_material_id', 'issue_material.unit_cost as unit_cost')
        ->get();

    foreach ($raw as $r) {
        if ($r->remaining_qty >= $qty) {
            $q = $r->remaining_qty - $qty;
            $this->updateRemaining($q, $r->issue_material_id);
            break; // Exit the loop once the quantity is deducted
        } else {
            $qty -= $r->remaining_qty;
            $this->updateRemaining(0, $r->issue_material_id); // Deduct all remaining quantity from this record
        }
    }
}

public function updateRemaining($q, $issue_material_id)
{
    DB::table('issue_material')
        ->where('issue_material_id', $issue_material_id)
        ->update([
            'calculation' => $q
        ]);
}

    

    
    
    public function getProductQuantity(Request $request)
    {
        $finish_product_id = $request->input('finish_product_id');
        
        $quantity = DB::table('finish_product_stock')
                      ->where('finish_product_id', $finish_product_id)
                      ->value('quantity');
     return response()->json(['success' => true, 'quantity' => $quantity]);
        
    }

    public function index()
    {
        $fp = DB::table('finish_product_stock')
        ->leftJoin('finish_product', 'finish_product_stock.finish_product_id', '=', 'finish_product.finish_product_id')
        ->select(
            'finish_product_stock.finish_product_id',
            'finish_product.product_name as product_name'
        )
        ->where('finish_product_stock.quantity', '>', 0)
        ->whereNotNull('finish_product_stock.quantity')
        ->get();
        return view('finishproduct.list',compact('fp'));
    }
    public function oldlistview(Request $request)
    {
       
        return view('finishproduct.oldproduct');
    }
    public function oldview()
    {
        $fp = FinishProduct::all();
        return view('finishproduct.oldproduct',compact('fp'));
    }
    public function detail(string $id)
    {
        $fp = FinishProduct::find($id);
        return view('finishproduct.detail',compact('fp'));
    }
    public function detaillist(Request $request,string $id)
    {
        if ($request->ajax()) {
            $draw = $request->input('draw');
    
            $query = DB::table('product_materials')
                ->leftJoin('finish_product', 'product_materials.finish_product_id', '=', 'finish_product.finish_product_id')
                ->leftJoin('raw_material', 'product_materials.raw_material_id', '=', 'raw_material.raw_material_id')
                ->select(
                    'product_materials.material_qty',
                    
                   
                   'raw_material.name as material_name',
                   'finish_product.product_name as material',
                  
                )->where('product_materials.finish_product_id', $id);
    
            $searchValue = $request->input('search.value');
    
            if ($searchValue) {
                $query->where('finish_product.product_name', 'like', '%' . $searchValue . '%');
            }
    
            $totalRecords = $query->count();
            $query->orderBy('product_materials.product_materials_id', 'desc');
    
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $query->skip($start)->take($length);
    
            $machines = $query->get();
    
            $data = [
                'draw' => (int) $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $machines,
            ];
    
            return response()->json($data);
        }
       
    }

   
        
        /**
         * Show the form for creating a new resource.
         */
     public function create()
    {
        $RawMaterial=RawMaterial::all();
        return view('finishproduct.add',compact('RawMaterial'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FinishProductRequest $request)
{
    $request->validated();
    $product_name = $request->post('product_name');
    $raw_material_id = $request->post('raw_material_id');
    $quantity = $request->post('quantity');

    // Check for duplicate raw_material_id values
    $counts = array_count_values($raw_material_id);
    // Find duplicate values
    $duplicates = array_filter($counts, function($count) {
        return $count > 1;
    });
    // Check if there are any duplicates
    if (!empty($duplicates)) {
        $msg = 'Duplicate raw materials found: ' . implode(', ', array_keys($duplicates));
        session()->flash('error', $msg);
        return redirect('finishproduct'); 
    }

    // If no duplicates, proceed with the insertion
    $data = [
        'product_name' => $product_name
        
    ];
    $insertedId = DB::table('finish_product')->insertGetId($data);

    foreach($raw_material_id as $i => $mid){
        $parr = [
            'finish_product_id' => $insertedId,
            'raw_material_id' => $mid,
           'material_qty' => $quantity[$i],
        ];
        DB::table('product_materials')->insert($parr);
    }

    session()->flash('success', 'Finish Product created successfully!');
    return redirect('finishproduct');
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
    public function edit($id)
    {
        // Retrieve the finish product details
        $finishProduct = DB::table('finish_product')
            ->where('finish_product_id', $id)
            ->first();
    
        // Join product_materials with raw_material to get the material details
        $productMaterials = DB::table('product_materials')
            ->join('raw_material', 'product_materials.raw_material_id', '=', 'raw_material.raw_material_id')
            ->where('finish_product_id', $id)
            ->select('product_materials.*', 'raw_material.name as raw_material_name')
            ->get();
    
        // Fetch all raw materials for the dropdown
        $RawMaterial = DB::table('raw_material')->get();
    
        return view('finishproduct.edit', compact('finishProduct', 'productMaterials', 'RawMaterial'));
    }
    
    public function update(FinishProductRequest $request, $id)
    {
        $request->validated();
        $product_name = $request->post('product_name');
        $raw_material_id = $request->post('raw_material_id');
        $quantity = $request->post('quantity');
    
        // Check for duplicate raw_material_id values
        $counts = array_count_values($raw_material_id);
        $duplicates = array_filter($counts, function($count) {
            return $count > 1;
        });
    
        if (!empty($duplicates)) {
            $msg = 'Duplicate raw materials found: ' . implode(', ', array_keys($duplicates));
            session()->flash('error', $msg);
            return redirect()->route('finishproduct.edit', $id); 
        }
    
        // Update finish product
        DB::table('finish_product')->where('finish_product_id', $id)->update(['product_name' => $product_name]);
    
        // Update product materials
        DB::table('product_materials')->where('finish_product_id', $id)->delete(); // Clear existing materials
        foreach($raw_material_id as $i => $mid){
            $parr = [
                'finish_product_id' => $id,
                'raw_material_id' => $mid,
                'material_qty' => $quantity[$i],
            ];
            DB::table('product_materials')->insert($parr);
        }
    
        session()->flash('success', 'Finish Product updated successfully!');
        return redirect('finishproduct');
    }
    
    public function oldstore(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'finish_product_id' => 'required',
        'quantity' => 'required|numeric|min:1',
        'unit_price' => 'required|numeric|min:1',
    ]);

    // Check if the finish_product_id already exists in the table
    $exists = DB::table('old_fproduct_stock')
                ->where('finish_product_id', $request->input('finish_product_id'))
                ->exists();

    if ($exists) {
        
        return redirect()->back()->withErrors(['finish_product_id' => 'This finish product has already been added.']);
    }

    // Insert the new record into the table
    DB::table('old_fproduct_stock')->insert([
        'finish_product_id' => $request->input('finish_product_id'),
        'quantity' => $request->input('quantity'),
        'unit_cost_price' => $request->input('unit_price'),
    ]);

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Data inserted successfully!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function listing(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder
        $query = FinishProduct::query();
    
        // Apply sorting
        // $sortColumnIndex = $request->input('order.0.column');
        // $sortDirection = $request->input('order.0.dir');
        // $sortColumnName = $request->input("columns.$sortColumnIndex.data");
        $query->orderBy('finish_product_id', 'desc');
    
        // Apply searching
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('product_name', 'like', "%$searchValue%");
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
}
