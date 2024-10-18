<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\Purchase; 
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\Rubberreq;
use DB;
class PurchaseController extends Controller
{
    /** 
     * Display a listing of the resource.
     */
    public function index()
    {
       
      
       return view('purchase.list');
    }
  
    public function return_store(Request $request)
{
    // Start a database transaction
    DB::beginTransaction();
    
    try {
        // Validate the request
        $validatedData = $request->validate([
            'supplier_id' => 'required',
            'total.*' => 'required',
            'purchase_id' => 'required',
            'raw_material_id' => 'required',
            'return_qty.*' => 'required',
            'unit_price' => 'required',
            'transport_charges' => 'nullable',
            'grand_total' => 'nullable',
            'date' => 'required',
            'unit_id' => 'required',
            
        ]);

        // Extract necessary inputs from validated data
        $supplierId = $validatedData['supplier_id'];
        $purchaseId = $validatedData['purchase_id'];
        $rawMaterialIds = $validatedData['raw_material_id'];
        $returnQuantities = $validatedData['return_qty'];
        $unitPrices = $validatedData['unit_price'];
        $transportCharges = $validatedData['transport_charges'] ?? 0;
        $grandTotal = $validatedData['grand_total'] ?? 0;
        $date = $validatedData['date'];
        $unit_id = $validatedData['unit_id'];
        $total = $validatedData['total'];

        // Process each raw material return
        foreach ($rawMaterialIds as $i => $rawMaterialId) {
            // Check if the quantity to return is valid
            if ($returnQuantities[$i] < 0) {
                throw new \Exception('Invalid return quantity for raw material ID: ' . $rawMaterialId);
            }
            $unitName = DB::table('unit')
            ->where('unit_id', $unit_id[$i])
            ->value('name');

            $convertedQuantityinches= $this->convertToInches($returnQuantities[$i], $unitName);
            // Update raw stock by reducing the available quantity
            $existingRawStock = DB::table('raw_stock')
                ->where('raw_material_id', $rawMaterialId)
                ->first();

            if ($existingRawStock) {
                // Ensure not to reduce quantity below zero
                $newAvailableQuantity = max(0, $existingRawStock->available_quantity - $convertedQuantityinches);
                DB::table('raw_stock')
                    ->where('raw_material_id', $rawMaterialId)
                    ->update(['available_quantity' => $newAvailableQuantity]);
            } else {
                throw new Exception('Raw material ID not found in stock: ' . $rawMaterialId);
            }

            // Insert into purchase return detail
            $purchaseReturnDetailData = [
                'purchase_material_id' => $purchaseId,
                'raw_material_id' => $rawMaterialId,
                'return_quantity' => $convertedQuantityinches,
                'unit_id' => $unit_id[$i],
                'unit_price' => $unitPrices[$i],
                'total_amount' => $grandTotal,
                'return_date' => $date,
            ];
            DB::table('purchase_return_detail')->insert($purchaseReturnDetailData);


            $currentValues = DB::table('purchase_material_detail')
            ->select('quantity','remaining_qty','total_amount')
            ->where('purchase_material_id', $purchaseId)
            ->where('raw_material_id', $rawMaterialId)
            ->first();

            if ($currentValues) {
                // Calculate new paid_amount and remaining_amount
                $newreturnQuantities = $currentValues->quantity - $returnQuantities[$i];
                $newremaining_qty = $currentValues->remaining_qty - $convertedQuantityinches;
                $newtotal_amount = $currentValues->total_amount - $total[$i];
                
              
    
                // Update the record in the database
                DB::table('purchase_material_detail')
                ->where('purchase_material_id', $purchaseId)
                ->where('raw_material_id', $rawMaterialId)
                    ->update([
                        'quantity' => $newreturnQuantities,
                        'remaining_qty' => $newremaining_qty,
                        'total_amount' => $newtotal_amount,
                    ]);
                }

        }

        $currentValues = DB::table('purchase_material')
                ->select('grand_total')
                ->where('purchase_material_id', $purchaseId)
                ->first();
    
                if ($currentValues) {
                    // Calculate new paid_amount and remaining_amount
                    $newgrandTotal = $currentValues->grand_total - $grandTotal;
                  
        
                    // Update the record in the database
                    DB::table('purchase_material')
                        ->where('purchase_material_id', $purchaseId)
                        ->update([
                            'grand_total' => $newgrandTotal,
                        ]);

                      
    
                       
                }

                $supplier = DB::table('supplier')
                ->select('remaining_amount')
                ->where('supplier_id', $supplierId)
                ->first();

                if ($supplier) {
                    // Calculate new paid_amount and remaining_amount
                    $newremaining_amount = $supplier->remaining_amount - $grandTotal;
                  
        
                    // Update the record in the database
                    DB::table('supplier')
                        ->where('supplier_id', $supplierId)
                        ->update([
                            'remaining_amount' => $newremaining_amount,
                        ]);

                      
    
                       
                }

        // Update the supplier ledger
        DB::table('supplier_ledger')->insert([
            'supplier_id' => $supplierId,
            'status' => 'Purchase Return',
            'narration' => 'Returned Purchase for ID: ' . $purchaseId,
            'debit' => $grandTotal,  // Assuming returns reduce the supplier's credit
            'running_balance' => 0, // Update running balance
        ]);

        // Commit the transaction
        DB::commit();

        // Flash success message to session
        session()->flash('success', 'Purchase return processed successfully!');
        return redirect()->route('purchase.index'); // Adjust the route as needed

    } catch (Exception $e) {
        // Rollback the transaction
        DB::rollBack();

        session()->flash('error', 'Error: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

    public function p_return_view(string $id)
{
    $purchase = DB::table('purchase_material')
        ->leftJoin('supplier', 'purchase_material.supplier_id', '=', 'supplier.supplier_id')
        ->leftJoin('purchase_material_detail', 'purchase_material.purchase_material_id', '=', 'purchase_material_detail.purchase_material_id') // Correct join condition
        ->leftJoin('raw_material', 'purchase_material_detail.raw_material_id', '=', 'raw_material.raw_material_id')
        ->leftJoin('unit', 'purchase_material_detail.unit_id', '=', 'unit.unit_id')
        ->select(
            'purchase_material_detail.purchase_material_detail_id',
            'purchase_material_detail.unit_price',
            'purchase_material_detail.total_amount',
            'purchase_material_detail.quantity',
            'unit.name as unit_name',
            'raw_material.name as raw_material_name',
            'purchase_material.purchase_material_id',
            'purchase_material.purchase_date',
            'purchase_material.transportation_amount',
            'purchase_material.grand_total',
            'purchase_material.total_paid',
            'purchase_material.remaining_amount',
            'supplier.name as supplier_name',
            'supplier.supplier_id'
        )
        ->where('purchase_material.purchase_material_id', $id)
        ->first();

    $details = DB::table('purchase_material_detail')
        ->leftJoin('raw_material', 'purchase_material_detail.raw_material_id', '=', 'raw_material.raw_material_id')
        ->leftJoin('unit', 'purchase_material_detail.unit_id', '=', 'unit.unit_id')
        ->select(
            'purchase_material_detail.purchase_material_detail_id',
            'purchase_material_detail.unit_price',
            'purchase_material_detail.total_amount',
            'purchase_material_detail.quantity',
            'unit.name as unit_name',
            'raw_material.name as raw_material_name',
            'raw_material.raw_material_id',
            'unit.unit_id'
        )
        ->where('purchase_material_detail.purchase_material_id', $purchase->purchase_material_id)
        ->get();

    // Fetch suppliers for dropdown
   
    
    return view('purchase.return', compact('purchase', 'details'));
}

  
    public function listing(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder
        $query = DB::table('purchase_material')
            ->leftJoin('supplier', 'purchase_material.supplier_id', '=', 'supplier.supplier_id')
            ->select(
                'purchase_material.purchase_material_id',
                'purchase_material.purchase_date',
                'purchase_material.transportation_amount',
                'purchase_material.grand_total',
                'purchase_material.remaining_amount',
                'purchase_material.total_paid',
                'supplier.name as supplier_name',
                'supplier.supplier_id as supplier_id'
            );
    
        // List of valid columns for sorting
        $validSortColumns = [
            'purchase_material_id',
            'purchase_date',
            'transportation_amount',
            'grand_total',
            'remaining_amount',
            'total_paid',
            'supplier_name',
            'supplier_id'
        ];
    
        // Apply sorting
        // $sortColumnIndex = $request->input('order.0.column');
        // $sortDirection = $request->input('order.0.dir', 'asc'); // Default to 'asc' if not provided
        // if (!in_array($sortDirection, ['asc', 'desc'])) {
        //     $sortDirection = 'asc'; // Fallback to 'asc' if the direction is invalid
        // }
        // $sortColumnName = $request->input("columns.$sortColumnIndex.data", 'purchase_material.purchase_material_id'); // Default column
    
        // Validate the sort column
        // if (!in_array($sortColumnName, $validSortColumns)) {
        //     $sortColumnName = 'purchase_material.purchase_material_id'; // Default to a valid column if invalid column is provided
        // }
    
        $query->orderBy('purchase_material_id', 'desc');
    
        // Apply searching
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('supplier.name', 'like', "%$searchValue%")
                      ->orWhere('purchase_material.purchase_material_id', 'like', "%$searchValue%");
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
        $results = $query->get();
    
        // Modify the results to include total_amount and pb_purchase_material_id
        $modifiedResults = [];
        foreach ($results as $result) {
            // Calculate total_amount
            // $totalAmount = $result->transportation_amount + $result->grand_total;
    
            // Create a modified result object
            $modifiedResult = (object)[
                'purchase_material_id' => $result->purchase_material_id,
                'invoice' => 'IN - ' . $result->purchase_material_id,
                'purchase_date' => $result->purchase_date,
                'transportation_amount' => $result->transportation_amount,
                'grand_total' => $result->grand_total,
                'remaining_amount' => $result->remaining_amount,
                'total_paid' => $result->total_paid,
                'supplier_name' => $result->supplier_name,
                'supplier_id' => $result->supplier_id,
                // 'total_amount' => $totalAmount, // Include total_amount
            ];
    
            // Add modified result to the array
            $modifiedResults[] = $modifiedResult;
        }
    
        // Prepare the response data
        $data = [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
            'data' => $modifiedResults, // Data to be displayed in DataTables
        ];
    
        // Return the response as JSON
        return response()->json($data);
    }
    

    
    public function create()
{
    $supplier = Supplier::all();
    $types = [1, 3, 4,5];
    $raw = RawMaterial::whereIn('type', $types)->get();
    return view('purchase.add', compact('supplier', 'raw'));
}
    public function getunits(Request $request){
        $id=$request->post('id');
        $rawMaterial = DB::table('raw_material')
            ->join('unit', 'raw_material.unit_id', '=', 'unit.unit_id')
            ->where('raw_material.raw_material_id', $id)
            ->select('raw_material.*', 'unit.name as unit_name','unit.unit_id as unit_id')
            ->first();

        if (!$rawMaterial) {
            return response()->json(['error' => 'Raw material not found'], 404);
        }
        $data=['unit'=>$rawMaterial->unit_name,
                'unit_id'=>$rawMaterial->unit_id
        ];
        return $data;

    }

    
    
    public function store(PurchaseRequest $request)
    {
        // Start a database transaction
        DB::beginTransaction();
    
        try {
            // Validate the request
            $validatedData = $request->validated();
    
            // Extract necessary inputs from validated data
            $supplierId = $validatedData['supplier_id'];
            $rawMaterialIds = $validatedData['raw_material_id'];
            $units = $validatedData['unit_id'];
            $unitNames = $validatedData['unit_name'];
            $quantities = $validatedData['qty'];
            $unitPrices = $validatedData['unit_price'];
            $totals = $validatedData['total'];
            $transportCharges = $validatedData['transport_charges'];
            $grandTotal = $validatedData['gandtotal'];
            $date = $validatedData['date'];
            $paidAmount =0;
            $narration = 0;
    
            // Check for duplicate raw_material_id values
            $counts = array_count_values($rawMaterialIds);
            $duplicates = array_filter($counts, function($count) {
                return $count > 1;
            });
    
            if (!empty($duplicates)) {
                $msg = 'Duplicate raw materials found: ' . implode(', ', array_keys($duplicates));
                throw new Exception($msg);
            }
    
            $remainingAmount = $grandTotal - $paidAmount;
    
            
    
            if ($paidAmount > 0) {
                DB::table('supplier_account')->insert([
                    'supplier_id' => $supplierId,
                    'narration' => $narration,
                    'paid_amount' => $paidAmount,
                ]);
            }
    
            // Prepare purchase record data
            $purchaseData = [
                'supplier_id' => $supplierId,
                'purchase_date' => now()->toDateString(), // Use Carbon for date formatting
                'transportation_amount' => $transportCharges,
                'grand_total' => $grandTotal,
                'total_paid' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'purchase_date' => $date,
            ];
    
            // Insert purchase record and get its ID
            $purchaseMaterialId = DB::table('purchase_material')->insertGetId($purchaseData);


            $currentValues = DB::table('supplier')
                ->select('paid_amount', 'total_amount', 'remaining_amount')
                ->where('supplier_id', $supplierId)
                ->first();
    
                if ($currentValues) {
                    // Calculate new paid_amount and remaining_amount
                    $newPaidAmount = $currentValues->paid_amount + $paidAmount;
                    $newRemainingAmount = $currentValues->remaining_amount + $remainingAmount;
                    $totalAmount = $currentValues->total_amount + $grandTotal;
        
                    // Update the record in the database
                    DB::table('supplier')
                        ->where('supplier_id', $supplierId)
                        ->update([
                            'paid_amount' => $newPaidAmount,
                            'remaining_amount' => $newRemainingAmount,
                            'total_amount' => $totalAmount,
                        ]);
    
                        DB::table('supplier_ledger')->insert([
                            'supplier_id' => $supplierId,
                            'status' => 'Purchase',
                            'narration' => 'Purchase',
                            'credit' => $grandTotal,
                            'running_balance' => $newRemainingAmount, // Update running_balance
                            'purchase_material_id' => $purchaseMaterialId, 
                        ]);
                }
    
            // Process each raw material item
            foreach ($rawMaterialIds as $i => $rawMaterialId) {
                // Convert quantity to inches based on unit name
                $convertedQuantity = $this->convertToInches($quantities[$i], $unitNames[$i]);
                $convertedPrice = $this->convertPrice($unitPrices[$i], $unitNames[$i]);

                // Check if raw material exists in raw_stock table
                $existingRawStock = DB::table('raw_stock')
                    ->where('raw_material_id', $rawMaterialId)
                    ->first();
    
                if ($existingRawStock) {
                    // Raw material already exists, update available_quantity
                    $updatedQuantity = $existingRawStock->available_quantity + $convertedQuantity;
    
                    DB::table('raw_stock')
                        ->where('raw_material_id', $rawMaterialId)
                        ->update(['available_quantity' => $updatedQuantity]);
                } else {
                    // Raw material does not exist, insert new record
                    $rawStockData = [
                        'purchase_material_id' => $purchaseMaterialId,
                        'raw_material_id' => $rawMaterialId,
                        'available_quantity' => $convertedQuantity,
                    ];
    
                    DB::table('raw_stock')->insert($rawStockData);
                }
    
                
                $purchaseMaterialDetailData = [
                    'purchase_material_id' => $purchaseMaterialId,
                    'raw_material_id' => $rawMaterialId,
                    'unit_id' => $units[$i],
                    'quantity' => $quantities[$i],
                    'unit_price' => $unitPrices[$i],
                    'total_amount' => $totals[$i],
                    'convert_price' => $convertedPrice,
                    'remaining_qty' => $convertedQuantity,
                ];
                   
                // Insert purchase material detail
                DB::table('purchase_material_detail')->insert($purchaseMaterialDetailData);
            }
    
            // Commit the transaction
            DB::commit();
    
            // Flash success message to session
            session()->flash('success', 'Purchase created successfully!');
            return redirect('purchase');
    
        } catch (Exception $e) {
            // Rollback the transaction
            DB::rollBack();
    
            
            session()->flash('error', 'error-> ' . $e->getMessage());
    
            // Redirect back to purchase page
            return redirect('purchase');
        }
    
        // Redirect back to purchase page
        
    }
    

// Utility function to convert quantities to inches based on unit
private function convertToInches($quantity, $unit)
{
    switch (strtolower($unit)) {
        case 'meter(60)':
            return $quantity * 2340;
        case 'kg dori':
            return $quantity * 11592;
        case 'foot':
            return $quantity * 144;  // 1 foot = 12 inches
        case 'meter':
            return $quantity * 39.3701; // 1 meter = 39.3701 inches
        case 'gaz':
            return $quantity * 36; // 1 meter = 39.3701 inches
        case 'gaz (60)':
            return $quantity * 2160; 
        case 'gaz (56)':
            return $quantity * 2016;
        case 'kg(12x16)':
            return $quantity * 60;
        case 'kg(11x16)':
            return $quantity * 60;
        case 'kg(14x18)':
            return $quantity * 60;
        case 'kg(13x16)':
            return $quantity * 60;
        case 'kg(11x14)':
            return $quantity * 60;
        case 'kg(16x18)':
            return $quantity * 60;
        case 'kg(14x22)':
            return $quantity * 60;
        case 'kg(30x40)':
            return $quantity * 30;
        case 'kg(26x36)':
            return $quantity * 30;
        default:
            return $quantity; // If no unit matches, return the original quantity
    }
}
private function convertprice($unitPrices, $unit)
{
    switch (strtolower($unit)) {
        case 'meter(60)':
                 return $unitPrices / 2340;
        case 'kg dori':
                 return $unitPrices / 11592;
        case 'foot':
            return $unitPrices / 144; 
        case 'meter':
            return $unitPrices / 39.3701; 
        case 'gaz':
            return $unitPrices / 36;
        case 'gaz (60)':
            return $unitPrices / 2160;  
        case 'gaz (56)':
            return $unitPrices / 2016; 
            case 'kg(12x16)':
                return $unitPrices / 60;
            case 'kg(11x16)':
                return $unitPrices / 60;
            case 'kg(14x18)':
                return $unitPrices / 60;
            case 'kg(13x16)':
                return $unitPrices / 60;
            case 'kg(11x14)':
                return $unitPrices / 60;
            case 'kg(16x18)':
                return $unitPrices / 60;
            case 'kg(14x22)':
                return $unitPrices / 60;
            case 'kg(30x40)':
                return $unitPrices / 30;
            case 'kg(26x36)':
                return $unitPrices / 30;   
            
        default:
            return $unitPrices; 
    }
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
    public function rubberpurchase()
    {
       
      
      
    }
    public function rubber()
    {
        $supplier = Supplier::all();
        $raw = RawMaterial::where('type', 2)->get();
      
       return view('purchase.rubber',compact('supplier','raw'));
    }




 public function rubberstore(Rubberreq $request)
{
        DB::beginTransaction();
        try {
            // Validate the request
            $validatedData = $request->validated();
    
            // Extract necessary inputs from validated data
            $supplierId = $validatedData['supplier_id'];
            $rawMaterialIds = $validatedData['raw_material_id'];
            $units = $validatedData['unit_id'];
            $unitNames = $validatedData['unit_name'];
            $quantities = $validatedData['qty'];
            $unitPrices = $validatedData['unit_price'];
            $totals = $validatedData['total'];
            $transportCharges = $validatedData['transport_charges'];
            $grandTotal = $validatedData['gandtotal'];
            $paidAmount = 0;
            $narration = 0;
            $kilogram = $validatedData['kilogram'];
            $sheet = $validatedData['sheet'];
    
            // Check for duplicate raw_material_id values
            $counts = array_count_values($rawMaterialIds);
    
            // Find duplicate values
            $duplicates = array_filter($counts, function($count) {
                return $count > 1;
            });
    
            // Check if there are any duplicates
            if (!empty($duplicates)) {
                $msg = 'Duplicate raw materials found: ' . implode(', ', array_keys($duplicates));
                throw new \Exception($msg);
            }
    
            // Calculate remaining amount
            $remainingAmount = $grandTotal - $paidAmount;
    
            
    
            
    
            if ($paidAmount > 0) {
                DB::table('supplier_account')->insert([
                    'supplier_id' => $supplierId,
                    'narration' => $narration,
                    'paid_amount' => $paidAmount,
                ]);
            }
    
            // Prepare purchase record data
            $purchaseData = [
                'supplier_id' => $supplierId,
                'purchase_date' => now()->toDateString(), // Use Carbon for date formatting
                'transportation_amount' => $transportCharges,
                'grand_total' => $grandTotal,
                'total_paid' => $paidAmount,
                'remaining_amount' => $remainingAmount,
            ];
    
            // Insert purchase record and get its ID
            $purchaseMaterialId = DB::table('purchase_material')->insertGetId($purchaseData);

            $currentValues = DB::table('supplier')
                        ->select('paid_amount', 'total_amount', 'remaining_amount')
                        ->where('supplier_id', $supplierId)
                        ->first();

            if ($currentValues) {
                // Calculate new paid_amount and remaining_amount
                $newPaidAmount = $currentValues->paid_amount + $paidAmount;
                $newRemainingAmount = $currentValues->remaining_amount + $remainingAmount;
                $totalAmount = $currentValues->total_amount + $grandTotal;
    
                // Update the record in the database
                DB::table('supplier')
                    ->where('supplier_id', $supplierId)
                    ->update([
                        'paid_amount' => $newPaidAmount,
                        'remaining_amount' => $newRemainingAmount,
                        'total_amount' => $totalAmount,
                    ]);

                    DB::table('supplier_ledger')->insert([
                        'supplier_id' => $supplierId,
                        'status' => 'Purchase',
                        'narration' => 'Purchase',
                        'credit' => $grandTotal,
                        'running_balance' => $newRemainingAmount, // Update running_balance
                        'purchase_material_id' => $purchaseMaterialId, 
                    ]);
            }
    
            // Process each raw material item
            foreach ($rawMaterialIds as $i => $rawMaterialId) {
                $convertedPrice = $this->rubberconvertprice($totals[$i], $unitNames[$i], $quantities[$i]);
    
                // Check if raw material exists in raw_stock table
                $existingRawStock = DB::table('raw_stock')
                    ->where('raw_material_id', $rawMaterialId)
                    ->first();
    
                if ($existingRawStock) {
                    // Raw material already exists, update available_quantity
                    $updatedQuantity = $existingRawStock->available_quantity + $quantities[$i];
    
                    DB::table('raw_stock')
                        ->where('raw_material_id', $rawMaterialId)
                        ->update(['available_quantity' => $updatedQuantity]);
                } else {
                    // Raw material does not exist, insert new record
                    $rawStockData = [
                        'purchase_material_id' => $purchaseMaterialId,
                        'raw_material_id' => $rawMaterialId,
                        'available_quantity' => $quantities[$i],
                    ];
    
                    DB::table('raw_stock')->insert($rawStockData);
                }
    
                // Prepare purchase material detail data
                $purchaseMaterialDetailData = [
                    'purchase_material_id' => $purchaseMaterialId,
                    'raw_material_id' => $rawMaterialId,
                    'unit_id' => $units[$i],
                    'quantity' => $quantities[$i],
                    'kilogram' => $kilogram[$i],
                    'sheet' => $sheet[$i],
                    'unit_price' => $unitPrices[$i],
                    'total_amount' => $totals[$i],
                    'convert_price' => $convertedPrice,
                    'remaining_qty' => $quantities[$i],
                ];
    
                // Insert purchase material detail
                DB::table('purchase_material_detail')->insert($purchaseMaterialDetailData);
            }

            
    
            // Commit transaction
            DB::commit();
    
            // Flash success message to session
            session()->flash('success', 'Purchase created successfully!');
    
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
    
            // Flash error message to session
            session()->flash('error', $e->getMessage());
        }
    
        // Redirect back to purchase page
        return redirect('purchase');
}
    

    private function rubberconvertprice($totals, $unit,$quantities)
{
    switch (strtolower($unit)) {
        case 'inch rubber':
            return $totals / $quantities; 
           
        default:
            return $totals; 
    }
}

}
