<?php

namespace App\Http\Controllers;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Requests\supplierReq;
use App\Http\Resources\SupplierResource;
use Illuminate\Support\Facades\DB;
 
class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

            return view('supplier.list');
        
    }
    public function edit(string $id)
    {
        $supplier = DB::table('supplier')
        ->where('supplier_id',$id)
        ->first();

      
        return view('supplier.edit', compact('supplier'));
       
    }
    public function supplierview(string $id)
    {
        $supplier = Supplier::find($id);
            return view('supplier.payment',compact('supplier'));
        
    }
    public function supplierpaymentlist(Request $request,string $id)
    {
        if($request->ajax()){
            $draw = $request->input('draw');
            
            $query = DB::table('supplier_account')
            ->leftJoin('supplier', 'supplier_account.supplier_id', '=', 'supplier.supplier_id')
            ->select(
                'supplier_account.narration',
                'supplier_account.paid_amount',
                'supplier_account.created_at',
                'supplier.name as supplier_name'
            )
            ->where('supplier_account.supplier_id', $id);

            $searchValue = $request->input('search.value');

            if ($searchValue) {

                $query->where('supplier.name', 'like', '%' . $searchValue . '%');
            }

            $totalRecords = $query->count();
            $query->orderBy('supplier_account.supplier_account_id', 'desc');

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

   public function listing(Request $request)
{
    // Get the draw parameter
    $draw = $request->input('draw');

    // Initialize the query builder
    $query = Supplier::query();

    // Apply sorting
    $query->orderBy('supplier_id', 'desc');
    

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
    $suppliers = $query->get();

    // Prepare the response data
    $data = [
        'draw' => (int)$draw,
        'recordsTotal' => $totalRecords, // Total records without filtering
        'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
        'data' => $suppliers->map(function ($supplier) {
            return [
                'created_at' => $supplier->created_at,
                'supplier_id' => $supplier->supplier_id,
                'name' => $supplier->name,
                'company' => $supplier->company,
                'contact' => $supplier->contact,
                'address' => $supplier->address,
                'paid_amount' => number_format($supplier->paid_amount, 2),
                'total_amount' => number_format($supplier->total_amount, 2),
                'remaining_amount' => number_format($supplier->remaining_amount, 2),
                'op_balance' => number_format($supplier->op_balance, 2),
                
            ];
        })
    ];
 
    // Return the response as JSON
    return response()->json($data);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.add');
    }

    public function store(supplierReq $request)
{
    DB::beginTransaction();
    try{
      
        
        // Transaction type check karein
        $transactionType = $request->input('transaction_type');
        $debit = 0;
        $credit = 0;

        if ($transactionType === 'debit') {
            $debit = $request->input('op_balance');

            $supplier_id = DB::table('supplier')->insertGetId([
                'name' => $request->input('name'),
                'company' => $request->input('company'),
                'contact' => $request->input('contact'),
                'address' => $request->input('address'),
                'op_balance' => -$debit,
                'total_amount' => -$debit,
                'remaining_amount' => -$debit,
            ]);
        } elseif ($transactionType === 'credit') {
            $credit = $request->input('op_balance');

            $supplier_id = DB::table('supplier')->insertGetId([
                'name' => $request->input('name'),
                'company' => $request->input('company'),
                'contact' => $request->input('contact'),
                'address' => $request->input('address'),
                'op_balance' => $credit,
                'total_amount' => $credit,
                'remaining_amount' => $credit,
            ]);
        }

       

        DB::table('supplier_ledger')->insert([
            'supplier_id' => $supplier_id,
            'status' => 'Opening',
            'narration' => 'Opening',
            'debit' => $debit,
            'credit' => $credit,
            'running_balance' => $request->input('op_balance'),
        ]);

        DB::commit();

        session()->flash('success', 'Supplier added successfully!');
        return redirect('suppliers');
    }
    catch (\Exception $e) 
    {
        DB::rollBack();
        session()->flash('error', 'An error occurred ->' . $e->getMessage());
        return back()->withInput();
    }
}

    

  
    public function ledger(string $id)
    {
        $supplier = DB::table('supplier')
        ->where('supplier_id', $id)
        ->first();

        return view('supplier.ledger',compact('supplier'));
    }


    public function paymentbyid(string $supplier_id,$paymentvoucher_id)
    {
        $supplier = DB::table('supplier')
        ->where('supplier_id', $supplier_id)
        ->first();

        $payment = DB::table('paymentvoucher')
            ->select(
                'paymentvoucher.narration',
                'paymentvoucher.amount as paid_amount',
                'paymentvoucher.created_at',
                'paymentvoucher.paymentvoucher_id'
            )
            ->where('paymentvoucher_id', $paymentvoucher_id)->first();

        

        return view('supplier.paymentbyid',compact('supplier','payment'));


    }
    public function purchasebyid(string $supplier_id,$purchase_material_id)
    {
        $supplier = DB::table('supplier')
        ->where('supplier_id', $supplier_id)
        ->first();

        $purchase_material = DB::table('purchase_material')->select(
            'purchase_material.purchase_material_id',
            'purchase_material.purchase_date',
            'purchase_material.transportation_amount',
            'purchase_material.grand_total',
            'purchase_material.total_paid',
            'purchase_material.remaining_amount',
            
        )
        ->where('purchase_material.purchase_material_id', $purchase_material_id)->first();

        return view('supplier.purchasebyid',compact('supplier','purchase_material'));


    }

public function ledgerlist(Request $request, string $id)
{
    $draw = $request->input('draw');
    $start = $request->input('start', 0);
    $length = $request->input('length', 10);
    $searchValue = $request->input('search.value');
    $page = $start / $length;

    // Fetch initial balance from the supplier table
    // $initialBalance = DB::table('supplier')
    //     ->where('supplier_id', $id)
    //     ->value('remaining_amount');

    // SQL query to calculate running balance with pagination
    $query = "
        WITH RunningBalance AS (
            SELECT
                bl.supplier_ledger_id,
                bl.debit,
                bl.credit,
                bl.supplier_id,
                bl.purchase_material_id,
                bl.paymentvoucher_id,
                bl.created_at,
                bl.status,
                bl.narration,
                @balance := @balance + bl.credit - bl.debit AS running_balance
            FROM
                (SELECT @balance := ?) AS var_init,
                supplier_ledger AS bl
            WHERE
                bl.supplier_id = ?
                " . ($searchValue ? "AND (bl.narration LIKE ? OR DATE(bl.created_at) LIKE ?)" : "") . "
            ORDER BY
                bl.supplier_ledger_id ASC
        )
        SELECT *
        FROM RunningBalance
        ORDER BY supplier_ledger_id DESC
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
    $totalRecords = DB::table('supplier_ledger')
        ->where('supplier_id', $id)
        ->when($searchValue, function ($query, $searchValue) {
            return $query->where(function ($query) use ($searchValue) {
                $query->where('narration', 'like', "%$searchValue%")
                    ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%");
            });
        })
        ->count();

        
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



        public function show(string $id)
        {
            //
        }

       
       

        /**
         * Update the specified resource in storage.
         */
        public function update(supplierReq $request, $id)
        {
            DB::beginTransaction();
            try {
                // Transaction type check karein
                $transactionType = $request->input('transaction_type');
                $new_op_balance = str_replace(',', '', $request->input('op_balance'));
                
                // Existing op_balance ko fetch karein
                $existingSupplier = DB::table('supplier')->where('supplier_id', $id)->first();
                $existing_op_balance = $existingSupplier->op_balance;
        
                // Debits aur credits ko initialize karein
                $debit = 0;
                $credit = 0;
                
                // Total debits aur credits ko sum karein
                $debitsum = DB::table('supplier_ledger')
                    ->where('supplier_id', $id)
                    ->where('status', 'Purchase Return')
                    ->sum('debit');
                
                $creditsum = DB::table('supplier_ledger')
                    ->where('supplier_id', $id)
                    ->where('status', 'Purchase')
                    ->sum('credit');
                

                

                // Balance difference calculate karein
                $balance_difference = $new_op_balance - $existing_op_balance;
                
                $total_amount = 0;
                $remaining_amount = 0;
                
                if ($transactionType === 'debit') {
                    $debit = $new_op_balance;
        
                    // Total aur remaining amount calculation for debit
                    $total_amount = ($debitsum - $creditsum) + $new_op_balance;
                    $remaining_amount = -$total_amount;  // Debit case mein remaining amount negative hoga
        
                    // Supplier ko update karein with debit transaction
                    DB::table('supplier')->where('supplier_id', $id)->update([
                        'name' => $request->input('name'),
                        'company' => $request->input('company'),
                        'contact' => $request->input('contact'),
                        'address' => $request->input('address'),
                        'op_balance' => -$debit,
                        'total_amount' => -$total_amount,
                        'remaining_amount' => $remaining_amount,
                    ]);
                } elseif ($transactionType === 'credit') {
                    $credit = $new_op_balance;
        
                    // Total aur remaining amount calculation for credit
                    $total_amount = ($creditsum - $debitsum) + $new_op_balance;
                    $remaining_amount = $total_amount;  // Credit case mein remaining amount positive hoga
        
                    // Supplier ko update karein with credit transaction
                    DB::table('supplier')->where('supplier_id', $id)->update([
                        'name' => $request->input('name'),
                        'company' => $request->input('company'),
                        'contact' => $request->input('contact'),
                        'address' => $request->input('address'),
                        'op_balance' => $credit,
                        'total_amount' => $total_amount,
                        'remaining_amount' => $remaining_amount,
                    ]);
                }
        
                // Ledger table ko bhi update karein
                DB::table('supplier_ledger')
                    ->where('supplier_id', $id)
                    ->where('status', 'Opening')
                    ->update([
                        'debit' => $transactionType === 'debit' ? $debit : 0,
                        'credit' => $transactionType === 'credit' ? $credit : 0,
                        'running_balance' => $remaining_amount, // Running balance ko sync karein
                    ]);
        
                DB::commit();
        
                session()->flash('success', 'Supplier updated successfully!');
                return redirect('suppliers');
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('error', 'An error occurred ->' . $e->getMessage());
                return back()->withInput();
            }
        }
        
        
        


        /**
         * Remove the specified resource from storage.
         */
        public function destroy(string $id)
        {
            //
        }
 public function getsupplier(string $id)
        {
            
            $supplier = DB::table('supplier')
            ->where('supplier_id', $id)
            ->first();
            // $supplier = DB::table('purchase_material')->where('supplier_id', $id)->get();
            
            if (!$supplier) {
                // Handle case when no suppliers found
                return response()->json(['message' => 'No suppliers found for this ID'], 404);
            }
            else{
            // Return all matching suppliers
            // return response()->json($supplier);
            return view('supplier.purchase_detail',compact('supplier'));
            }
    }
    public function purchasedetail(Request $request, string $id)
    {
       
        $draw = $request->input('draw');
        
        
        $query = DB::table('purchase_material')
        ->leftJoin('supplier', 'purchase_material.supplier_id', '=', 'supplier.supplier_id')
        ->select(
            'purchase_material.purchase_material_id',
            'purchase_material.purchase_date',
            'purchase_material.transportation_amount',
            'purchase_material.grand_total',
            'purchase_material.total_paid',
            'purchase_material.remaining_amount',
            'supplier.name as supplier_name'
        )
        ->where('purchase_material.supplier_id', $id); // Filter by supplier_id
        
       
       
        $query->orderBy('purchase_material.purchase_material_id', 'desc');
        
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

        $modifiedResults = [];
        foreach ($results as $result) {
            // Calculate total_amount
            // $totalAmount = $result->transportation_amount + $result->grand_total;
    
            // Create a modified result object
            $modifiedResult = (object)[
                'purchase_material_id' => $result->purchase_material_id,
                'invoice_no' => 'IN - ' . $result->purchase_material_id,
                'purchase_date' => $result->purchase_date,
                'transportation_amount' => $result->transportation_amount,
                'grand_total' => $result->grand_total,
                'remaining_amount' => $result->remaining_amount,
                'total_paid' => $result->total_paid,
                'supplier_name' => $result->supplier_name,
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


    public function materialdetail($id, $purchase_detail_id) {
        $supplier = DB::table('supplier')
        ->where('supplier_id', $id)
        ->first();

     if (!$supplier) {
        return response()->json(['message' => 'No supplier found for this ID'], 404);
        }

        return view('supplier.material_detail', compact('supplier', 'purchase_detail_id'));
    }

    public function materialdetailinfo(Request $request, $id, $purchase_detail_id) {
        $draw = $request->input('draw');
        
        $query = DB::table('purchase_material_detail')
            ->leftJoin('purchase_material', 'purchase_material.purchase_material_id', '=', 'purchase_material_detail.purchase_material_id')
            ->leftJoin('raw_material', 'purchase_material_detail.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('unit', 'purchase_material_detail.unit_id', '=', 'unit.unit_id')
            ->select(
                'purchase_material_detail.purchase_material_detail_id',
                'purchase_material_detail.unit_price',
                'purchase_material_detail.total_amount',
                'purchase_material_detail.quantity',
                'unit.name as unit_name',
                'raw_material.name as raw_material_name'
            )
            ->where('purchase_material_detail.purchase_material_id', $purchase_detail_id);
        
        // Apply sorting
       
        $query->orderBy('purchase_material_detail.purchase_material_detail_id', 'desc');
        
        // Apply searching
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
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
        
        // Prepare the response data
        $data = [
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
            'data' => $results, // Data to be displayed in DataTables
        ];
        
        // Return the response as JSON
        return response()->json($data);
    }
    public function supplierpaymentstore(Request $request)
{
    // Start a transaction
    DB::beginTransaction();

    try {
        // Fetch the supplier using the supplier_id from the request
        $supplier = Supplier::findOrFail($request->supplier_id);

        // Get the current remaining amount of the supplier
        $runningBalance = $supplier->remaining_amount;

        // Insert a record into the supplier_account table
        DB::table('supplier_account')->insert([
            'supplier_id' => $request->supplier_id,
            'narration' => $request->narration, 
            'paid_amount' => $request->amount,
        ]);

        // Insert a record into the supplier_ledger table
        DB::table('supplier_ledger')->insert([
            'supplier_id' => $request->supplier_id,
            'status' => 'Payment',
            'narration' => $request->narration,
            'credit' => $request->amount,
            'running_balance' => $runningBalance - $request->amount, // Update running_balance
        ]);

        // Update the supplier's remaining amount
        $supplier->remaining_amount = $runningBalance - $request->amount;
        $supplier->paid_amount +=$request->amount;
        $supplier->save();

        // Commit the transaction
        DB::commit();

        // Flash a success message to the session
        session()->flash('success', 'Payment Added successfully!');
        return redirect()->back();
    } catch (\Throwable $th) {
        // Rollback the transaction in case of an error
        DB::rollBack();

        // Flash an error message to the session
        session()->flash('error', $th->getMessage());
        return redirect()->back();
    }
}

    
    
    
}

