<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\karahi;
use App\Models\Vendor;
use App\Models\Machine;
use App\Models\IssueKarahi;

use App\Models\design;
use App\Http\Requests\VendorReq;
use App\Models\RawMaterial;
use DB;

class karhaiController extends Controller
{

    public function issueprint($id)
    {
        $query = DB::table('issue_karahi_material')
            ->leftJoin('raw_material', 'issue_karahi_material.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('karai_vendor', 'issue_karahi_material.karai_vendor_id', '=', 'karai_vendor.karai_vendor_id')
        
            ->select(
                'issue_karahi_material.issue_karahi_material_id',
                'issue_karahi_material.created_at',
                'issue_karahi_material.available_qty',
                'issue_karahi_material.issue_qty',
                'raw_material.name as raw_material_name',
                'karai_vendor.name as karai_vendor_name',
            )
            ->where('issue_karahi_material.issue_karahi_material_id',$id)
            ->first();

            // dd($query);
    
        return view('print.karahiissue', compact('query'));
    }



    public function print($id)
    {
        $query = DB::table('recieve_karahi_material_detail')
            ->leftJoin('raw_material', 'recieve_karahi_material_detail.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('receive_karahi_material', 'recieve_karahi_material_detail.receive_karahi_material_id', '=', 'receive_karahi_material.receive_karahi_material_id')
            ->leftJoin('karai_machine', 'recieve_karahi_material_detail.karai_machine_id', '=', 'karai_machine.karai_machine_id')
            ->leftJoin('karai_vendor', 'receive_karahi_material.karai_vendor_id', '=', 'karai_vendor.karai_vendor_id')
        
            ->select(
                'receive_karahi_material.receive_karahi_material_id',
                'receive_karahi_material.receive_date',
                'recieve_karahi_material_detail.sheets',
                'recieve_karahi_material_detail.quantity',
                'recieve_karahi_material_detail.used_material_qty',
                'recieve_karahi_material_detail.used_material_cost',
                'recieve_karahi_material_detail.unit_price',
                'recieve_karahi_material_detail.total',
                'karai_machine.head_code as karai_machine_head_code',
                'raw_material.name as raw_material_name',
                'karai_vendor.name as karai_vendor_name',
            )
            ->where('recieve_karahi_material_detail.receive_karahi_material_id',$id)
            ->get();

            // dd($query);
    
        return view('print.karahirec', compact('query'));
    }

    public function paymentbyid(string $id,$paymentvoucher_id)
    {
        $supplier = DB::table('karai_vendor')
        ->where('karai_vendor_id', $id)
        ->first();

        $payment = DB::table('paymentvoucher')
            ->select(
                'paymentvoucher.narration',
                'paymentvoucher.amount as paid_amount',
                'paymentvoucher.created_at',
                'paymentvoucher.paymentvoucher_id'
            )
            ->where('paymentvoucher_id', $paymentvoucher_id)->first();

        

        return view('karahivendor.paymentbyid',compact('supplier','payment'));


    }
    public function recievebyid(string $id,$recieve_id)
    {
        $supplier = DB::table('karai_vendor')
        ->where('karai_vendor_id', $id)
        ->first();

        $purchase_material = DB::table('receive_karahi_material')->select(
            'receive_karahi_material.receive_karahi_material_id',
            'receive_karahi_material.receive_date',
            'receive_karahi_material.transport_amount',
            'receive_karahi_material.grand_total',
            'receive_karahi_material.paid_amount',
            'receive_karahi_material.remaining_amount',
            'receive_karahi_material.invoice_no',
            
        )
        ->where('receive_karahi_material.receive_karahi_material_id', $recieve_id)->first();

        return view('karahivendor.receivebyid',compact('supplier','purchase_material'));


    }

    public function ledger(string $id)
    {
        $karai_vendor = DB::table('karai_vendor')
        ->where('karai_vendor_id', $id)
        ->first();

        return view('karahivendor.ledger',compact('karai_vendor'));
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
    
     //  CASE 
                //     WHEN bl.credit > 0 THEN 'Cr'
                //     WHEN bl.debit > 0 THEN 'Dr'
                //     ELSE ''
                // END AS balance_type
    $query = "
        WITH RunningBalance AS (
            SELECT
                bl.karahivendor_ledger_id,
                bl.debit,
                bl.credit,
                bl.karai_vendor_id,
                bl.receive_karahi_material_id,
                bl.paymentvoucher_id,
                bl.created_at,
                bl.status,
                bl.narration,
                @balance := @balance + bl.credit - bl.debit AS running_balance
               
            FROM
                (SELECT @balance := ?) AS var_init,
                karahivendor_ledger AS bl
            WHERE
                bl.karai_vendor_id = ?
                " . ($searchValue ? "AND (bl.narration LIKE ? OR DATE(bl.created_at) LIKE ?)" : "") . "
            ORDER BY
                bl.karahivendor_ledger_id ASC
        )
        SELECT *
        FROM RunningBalance
        ORDER BY karahivendor_ledger_id DESC
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
    $totalRecords = DB::table('karahivendor_ledger')
        ->where('karai_vendor_id', $id)
        ->when($searchValue, function ($query, $searchValue) {
            return $query->where(function ($query) use ($searchValue) {
                $query->where('narration', 'like', "%$searchValue%")
                    ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%");
            });
        })
        ->count();
        // foreach ($results as &$result) {
        //     $result->running_balance .= ' ' . $result->balance_type;
        // }
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









    public function index()
    {
        $vendor = karahi::all();
        $rm = RawMaterial::where('type', 5)->get();
        return view('karahivendor.list',compact('vendor','rm'));
    }

    public function karahipayment(string $id)
    {
        $karahiVendor = karahi::find($id);
        return view('karahivendor.payment',compact('karahiVendor'));
    }

    public function karahipaymentstore(Request $request)
    {
        // Payment ko karahi_vendor_payment table mein insert karna
        DB::table('karahi_vendor_payment')->insert([
            'karai_vendor_id' => $request->karai_vendor_id,
            'narration' => $request->narration,
            'paid_amount' => $request->amount,
        ]);
    
        // karai_vendor table ko update karna
        $vendor = karahi::find($request->karai_vendor_id);
        $vendor->paid_amount += $request->amount;
        $vendor->remaining_amount -= $request->amount;
        $vendor->save();
    
        session()->flash('success', 'Payment Added successfully!');
        return redirect()->back();
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function karahipaymentdetail(Request $request ,string $id)
    { 
        $draw = $request->input('draw');
            
        $query = DB::table('karahi_vendor_payment')
        ->leftJoin('karai_vendor', 'karahi_vendor_payment.karai_vendor_id', '=', 'karai_vendor.karai_vendor_id')
        ->select(
            'karahi_vendor_payment.narration',
            'karahi_vendor_payment.paid_amount',
            'karahi_vendor_payment.created_at',
            'karai_vendor.name as karai_vendor_name'
        )
        ->where('karahi_vendor_payment.karai_vendor_id', $id);

        $searchValue = $request->input('search.value');

        if ($searchValue) {

            $query->where('karai_vendor.name', 'like', '%' . $searchValue . '%');
        }

        $totalRecords = $query->count();
        $query->orderBy('_karahi_vendor_payment_id', 'desc');

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
    public function create()
    { 
       
        return view('karahivendor.add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'company' => 'required',
            'op_balance' => 'required',
            'contact' => 'required',
            'address' => 'required',
            'transaction_type' => 'required',
        ]);
    
        DB::beginTransaction();
        try {
            $transactionType = $request->input('transaction_type');
            $debit = 0;
            $credit = 0;


            if ($transactionType === 'debit') {
                $debit = $request->input('op_balance');

                $karahi = Karahi::create([
                    'name' => $validatedData['name'],
                    'company' => $validatedData['company'],
                    'contact' => $validatedData['contact'],
                    'address' => $validatedData['address'],
                    'op_balance' => -$debit,
                    'total_amount' => -$debit,
                    'remaining_amount' => -$debit
                ]);
    
            } elseif ($transactionType === 'credit') {
                $credit = $request->input('op_balance');

                $karahi = Karahi::create([
                    'name' => $validatedData['name'],
                    'company' => $validatedData['company'],
                    'contact' => $validatedData['contact'],
                    'address' => $validatedData['address'],
                    'op_balance' => $validatedData['op_balance'],
                    'total_amount' => $validatedData['op_balance'],
                    'remaining_amount' => $validatedData['op_balance']
                ]);
    
              
            }
           
    
            // Check if $karahi is created successfully
            if (!$karahi) {
                throw new \Exception('Karahi vendor creation failed');
            }
    
            // Ensure $karahi->id is not null
            if (is_null($karahi->karai_vendor_id)) {
                throw new \Exception('Karahi vendor ID is null');
            }
    
            // Insert into karahivendor_ledger
            DB::table('karahivendor_ledger')->insert([
                'karai_vendor_id' => $karahi->karai_vendor_id,
                'status' => 'Opening',
                'narration' => 'Opening',
                'debit' => $debit,
                'credit' => $credit,
                'running_balance' => $validatedData['op_balance'],
            ]);
    
            DB::commit();
            session()->flash('success', 'Added successfully!');
            return redirect('karahivendor');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred -> ' . $e->getMessage());
            return back()->withInput();
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
        $karahi = DB::table('karai_vendor')
        ->where('karai_vendor_id',$id)
        ->first();

      
        return view('karahivendor.edit', compact('karahi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'company' => 'required',
                'op_balance' => 'required',
                'contact' => 'required',
                'address' => 'required',
                'transaction_type' => 'required',
            ]);
    
            // Transaction type check karein 
            $transactionType = $request->input('transaction_type');
            $debit = 0;
            $credit = 0;
            $new_op_balance = str_replace(',', '', $request->input('op_balance'));
    
            // Pehle existing op_balance ko fetch karein
            $existingVendor = DB::table('karai_vendor')->where('karai_vendor_id', $id)->first();
            $existing_op_balance = $existingVendor->op_balance;

          
            $debitsum = DB::table('karahivendor_ledger')
            ->where('karai_vendor_id', $id)
            ->where(function($query) {
                $query->where('status', 'return')
                      ->orWhere('status', 'Payment');
            })
            ->sum('debit');

        $creditsum = DB::table('karahivendor_ledger')
            ->where('karai_vendor_id', $id)
            ->where('status', 'Recieve')
            ->sum('credit');
   
            // Difference nikalain pehle aur naya op_balance set karein
            $balance_difference = $new_op_balance - $existing_op_balance;
    
            if ($transactionType === 'debit') {
                $debit = $new_op_balance;

                $total_amount = ($debitsum - $creditsum) + $new_op_balance;
                $remaining_amount = -$total_amount;
                DB::table('karai_vendor')->where('karai_vendor_id', $id)->update([
                    'name' => $request->input('name'),
                    'company' => $request->input('company'),
                    'contact' => $request->input('contact'),
                    'address' => $request->input('address'),
                    'op_balance' => -$debit,
                    'total_amount' => -$total_amount,  // total amount adjust karein
                    'remaining_amount' =>$remaining_amount,  // remaining amount adjust karein
                ]);
            } elseif ($transactionType === 'credit') {
                $credit = $new_op_balance;

                $total_amount = ($creditsum - $debitsum) + $new_op_balance;
                $remaining_amount = $total_amount; 
                DB::table('karai_vendor')->where('karai_vendor_id', $id)->update([
                    'name' => $request->input('name'),
                    'company' => $request->input('company'),
                    'contact' => $request->input('contact'),
                    'address' => $request->input('address'),
                    'op_balance' => $credit,
                    'total_amount' =>$total_amount,  // total amount adjust karein
                    'remaining_amount' =>  $remaining_amount ,  // remaining amount adjust karein
                ]);
            }
    
            // Ledger me bhi balance update karein
            DB::table('karahivendor_ledger')
                ->where('karai_vendor_id', $id)
                ->where('status', 'Opening')
                ->update([
                    'debit' => $debit,
                    'credit' => $credit,
                    'running_balance' => 0,
                ]);
    
            DB::commit();
    
            session()->flash('success', 'karai_vendor updated successfully!');
            return redirect('karahivendor');
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
    public function karahiissuebyid(Request $request ,string $id)
    {
        if($request->ajax()){

            $draw = $request->input('draw');
            
            $query = DB::table('issue_karahi_material')
            ->leftJoin('raw_material', 'issue_karahi_material.raw_material_id', '=', 'raw_material.raw_material_id')
            ->select(
                'issue_karahi_material.issue_karahi_material_id',
                'issue_karahi_material.issue_qty',
                'issue_karahi_material.created_at',
                'issue_karahi_material.amount_issue',
                'raw_material.name as raw_material_name'
            )
            ->where('issue_karahi_material.karai_vendor_id', $id);

            $searchValue = $request->input('search.value');

            if ($searchValue) {

                $query->where('issue_karahi_material.created_at', 'like', '%' . $searchValue . '%');
            }

            $totalRecords = $query->count();
            $query->orderBy('issue_karahi_material_id', 'desc');
    
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
        
        $karahiVendor = karahi::find($id);

        $karavi_available_qty = DB::table('karavi_available_qty')
            ->select('karavi_available_qty.total_qty',
                        'karavi_available_qty.total_amount')
            ->where('karavi_available_qty.karai_vendor_id', $id)
            ->first();
        
           
        if (!$karahiVendor){
            abort(404); 
        }
        
        return view('karahivendor.issuedetail', compact('karahiVendor', 'karavi_available_qty'));
        
    }
   
    
    public function list(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder
        $query = Karahi::query();
    
        // Apply search functionality based on name
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%');
        }
    
        // Get total records before applying pagination
        $totalRecords = $query->count();
    
        // Apply sorting by created_at in descending order (newest first)
        $query->orderBy('karai_vendor_id', 'desc');
    
        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->skip($start)->take($length);
    
        // Fetch the data
        $machines = $query->get();
    
        // Prepare the response data
        $data = [
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
            'data' => $machines, // Data to be displayed in DataTables
        ];
    
        // Return the response as JSON
        return response()->json($data);
    }

    public function karahiissue(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'vendor' => 'required',
            'raw_material_id' => 'required',
            'quantity' => 'required|numeric|min:1', // Ensure quantity is numeric and positive
        ]);
    
        $vendorId = $validatedData['vendor'];
        $rawMaterialId = $validatedData['raw_material_id'];
        $issueQty = $validatedData['quantity'];

           
          

        DB::beginTransaction(); 
    
        try {

            $amount = $this->finalPrice($rawMaterialId, $issueQty);
                     $this->updateqtyForRaw($rawMaterialId, $issueQty);
            // Fetch available quantity of the selected raw material
            $availableQty = DB::table('raw_stock')
                              ->where('raw_material_id', $rawMaterialId)
                              ->value('available_quantity');
    
            if ($availableQty === null) {
                // If no record found, return an error
                return response()->json([
                    'success' => false,
                    'message' => 'Raw material not found.'
                ]);
            }
    
            if ($issueQty > $availableQty) {
                // If issue quantity is more than available quantity, return an error
                return response()->json([
                    'success' => false,
                    'message' => 'Issue quantity cannot be greater than available quantity.'
                ]);
            }
    
            // Insert the issued material into issue_karahi_material table
            $issueKarahiId = DB::table('issue_karahi_material')->insertGetId([
                'karai_vendor_id' => $vendorId,
                'raw_material_id' => $rawMaterialId,
                'issue_qty' => $issueQty,
                'available_qty' => $issueQty,
                'amount_issue' => $amount,
            ]);
    
            // Update the available quantity in the raw materials table
            DB::table('raw_stock')
                ->where('raw_material_id', $rawMaterialId)
                ->update(['available_quantity' => $availableQty - $issueQty]);
    
            // Update karavi_available_qty table
            $existingEntry = DB::table('karavi_available_qty')
                ->where('karai_vendor_id', $vendorId)
                ->first();
    
            if ($existingEntry) {
                DB::table('karavi_available_qty')
                    ->where('karai_vendor_id', $vendorId)
                    ->update(['total_qty' => DB::raw('total_qty + ' . $issueQty),'total_amount' => DB::raw('total_amount + ' . $amount)]);
            } else {
                DB::table('karavi_available_qty')->insert([
                    'issue_karahi_material_id' => $issueKarahiId,
                    'karai_vendor_id' => $vendorId,
                    'total_qty' => $issueQty,
                    'total_amount' => $amount,
                ]);
            }
    
            DB::commit(); // Commit the transaction
    
            return response()->json([
                'success' => true,
                'message' => 'Material issued successfully.',
                'id' => $issueKarahiId 
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.'
            ]);
        }
    }


    public function finalPrice($rid, $qty)
    {
        $total_amount = 0;
        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', $rid)
            ->whereNotNull('remaining_qty')
            ->where('remaining_qty', '>', 0)
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
            ->get();
    
        foreach ($raw as $r) {
            if ($r->remaining_qty >= $qty) {
                $q=$r->remaining_qty-$qty; 
                $amount = $r->up * $qty;
                $total_amount += $amount;
                return $total_amount;
            } else {
                $q=$r->remaining_qty-$r->remaining_qty;
                $amount = $r->up * $r->remaining_qty;
                $total_amount += $amount;
                $qty -= $r->remaining_qty;
                 
            }
        }
    
        return $total_amount;
    }
    public function updateqtyForRaw($rid, $qty)
    {
        
        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', $rid)
            ->whereNotNull('remaining_qty')
            ->where('remaining_qty', '>', 0)
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
            ->get();
    
        foreach ($raw as $r) {
            if ($r->remaining_qty >= $qty) {
                $q=$r->remaining_qty-$qty; 
                $this->updateRemaining($q,$r->pid);
               
            } else {
                $q=$r->remaining_qty-$r->remaining_qty;
                $qty -= $r->remaining_qty;
                 $this->updateRemaining($q,$r->pid);
            }
        }
    
       
    }
    public function updateRemaining($q,$pid){
        DB::table('purchase_material_detail')
        ->where('purchase_material_detail_id', $pid)
        ->update([
            'remaining_qty' => $q
        ]);
    }
    // public  function reduceQty($id,$qty){
    //     $raw = DB::table('raw_stock')
    //                 ->where('raw_material_id', $id)
    //                 ->select('raw_stock.available_quantity')
    //                 ->get()
    //                 ->first();
    //                 $fqty = $raw->available_quantity - $qty;
    //                 $updated = DB::table('raw_stock')
    //                     ->where('raw_material_id', $id)
    //                     ->update([
    //                         'available_quantity' => $fqty
    //                     ]);
    //         if($updated){
    //             return true;
    //         }else{
    //             return false;
    //         }
    // }
    


    public function receiving()
    {
        $vendor = karahi::all();
        $Machine = Machine::all();
        $design = RawMaterial::where('type', 0)->get();
        return view('karahivendor.receive',compact('vendor','Machine','design'));
    }
    public function receivingstore(Request $request)
{
    DB::beginTransaction();  

    try {
        $validatedData = $request->validate([
            'karai_vendor_id' => 'required',
            'raw_material_id' => 'required',
            'date' => 'required',
            'karai_machine_id' => 'required',
            'invoice_no' => 'required',
            'sheet.*' => 'required',
            'qty.*' => 'required',
            'unit_price.*' => 'required',
            'total.*' => 'required',
            'transport_charges' => 'required',
            'gandtotal' => 'required',
           
        ]);

        $karai_vendor_id = $validatedData['karai_vendor_id'];
        $date = $validatedData['date'];
        $invoice_no = $validatedData['invoice_no'];
        $karai_machine_id = $validatedData['karai_machine_id'];
        $sheet = $validatedData['sheet'];
        $raw_material_id = $validatedData['raw_material_id'];
        $qty = $validatedData['qty'];
        $unit_price = $validatedData['unit_price'];
        $total = $validatedData['total'];
        $transport_charges = $validatedData['transport_charges'];
        $gandtotal = $validatedData['gandtotal'];
        $paidamount = 0;
        $narration = 0;

        $remainingAmount = $gandtotal - $paidamount;

        // Update karai_vendor record
        $currentValues = DB::table('karai_vendor')
            ->select('paid_amount', 'total_amount', 'remaining_amount')
            ->where('karai_vendor_id', $karai_vendor_id)
            ->first();

        if ($currentValues) {
            $newPaidAmount = $currentValues->paid_amount + $paidamount;
            $newRemainingAmount = $currentValues->remaining_amount + $remainingAmount;
            $total_amount = $currentValues->total_amount + $gandtotal;

            DB::table('karai_vendor')
                ->where('karai_vendor_id', $karai_vendor_id)
                ->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                    'total_amount' => $total_amount,
                ]);
        }

        if ($paidamount > 0) {
            $karahi_vendor_payment = [
                'karai_vendor_id' => $karai_vendor_id,
                'narration' => $narration,
                'paid_amount' => $paidamount,
            ];

            DB::table('karahi_vendor_payment')->insert($karahi_vendor_payment);
        }

        $receive_karahi_material = [
            'karai_vendor_id' => $karai_vendor_id,
            'receive_date' => $date,
            'grand_total' => $gandtotal,
            'invoice_no' => $invoice_no,
            'paid_amount' => $paidamount,
            'remaining_amount' => $remainingAmount,
            'transport_amount' => $transport_charges,
        ];

        $receive_karahi_material_id = DB::table('receive_karahi_material')->insertGetId($receive_karahi_material);

        DB::table('karahivendor_ledger')->insert([
            'karai_vendor_id' => $karai_vendor_id,
            'status' => 'Recieve',
            'narration' => 'Recieve',
            'credit' => $gandtotal,
            'running_balance' => $newRemainingAmount, 
            'receive_karahi_material_id' => $receive_karahi_material_id, 
        ]);

        foreach ($raw_material_id as $i => $rawMaterialId) {
            $existingRawStock = DB::table('raw_stock')
                ->where('raw_material_id', $rawMaterialId)
                ->first();

            if ($existingRawStock) {
                $updatedQuantity = $existingRawStock->available_quantity + $qty[$i];

                DB::table('raw_stock')
                    ->where('raw_material_id', $rawMaterialId)
                    ->update(['available_quantity' => $updatedQuantity]);
            } else {
                $rawStockData = [
                    'raw_material_id' => $rawMaterialId,
                    'available_quantity' => $qty[$i],
                ];

                DB::table('raw_stock')->insert($rawStockData);
            }

            // Update vendor stock
            $vendorminus = DB::table('karavi_available_qty')
                ->where('karai_vendor_id', $karai_vendor_id)
                ->first();
            
               
            if ($vendorminus) {
                $machine = DB::table('karai_machine')->where('karai_machine_id', $karai_machine_id[$i])->first();

                


                if ($machine) {
        $updatedQuantity = $machine->size * $sheet[$i];
        $karaiVendorId = $karai_vendor_id;

        // Get the current total_qty and prevamount
            $karaviAvailableQty = DB::table('karavi_available_qty')
            ->where('karai_vendor_id', $karaiVendorId)
            ->first();

        if ($karaviAvailableQty) {
            $currentTotalQty = $karaviAvailableQty->total_qty;
            $prevamount = $vendorminus->total_amount;

        // Decrement total_qty
        $newTotalQty = $currentTotalQty - $updatedQuantity;

        // Calculate new total_amount
        $unitAmount = $prevamount / $currentTotalQty;

        $newunitamount  = $unitAmount* $newTotalQty;
        $updatedamount  = $prevamount -$newunitamount;

        // Update the table
        DB::table('karavi_available_qty')
            ->where('karai_vendor_id', $karaiVendorId)
            ->update([
                'total_qty' => $newTotalQty,
                'total_amount' => $newunitamount
            ]);
    } else {
        throw new \Exception("No record found for karai_vendor_id {$karaiVendorId}");
    }
        } else {
            throw new \Exception("Machine with ID {$karai_machine_id[$i]} not found.");
        }

            }

            $recieve_karahi_material_detail = [
                'receive_karahi_material_id' => $receive_karahi_material_id,
                'karai_machine_id' => $karai_machine_id[$i],
                'sheets' => $sheet[$i],
                'raw_material_id' => $rawMaterialId,
                'quantity' => $qty[$i],
                'unit_price' => $unit_price[$i],
                'total' => $total[$i],
                'remaining_qty' => $qty[$i],
                'used_material_qty' => $updatedQuantity,
                'used_material_cost' => $updatedamount,
            ];

            DB::table('recieve_karahi_material_detail')->insert($recieve_karahi_material_detail);
        }

        DB::commit(); 
        
        return redirect()->route('rec.printsslip', ['id' => $receive_karahi_material_id])->with('success', 'Issue Material successfully added.');
        
        // session()->flash('success', 'recieve   successfully!');
        // return redirect('karahivendor');

    } catch (\Exception $e) {
        DB::rollBack(); // Rollback transaction on error
        session()->flash('error', 'An error occurred: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}


    public function karahirec(Request $request ,string $id)
    {
        if($request->ajax()){

            $draw = $request->input('draw');
            
            $query = DB::table('receive_karahi_material')
            ->leftJoin('karai_vendor', 'receive_karahi_material.karai_vendor_id', '=', 'karai_vendor.karai_vendor_id')
            ->select(
                'receive_karahi_material.invoice_no',
                'receive_karahi_material.receive_karahi_material_id',
                'receive_karahi_material.receive_date',
                'receive_karahi_material.created_at',
                'receive_karahi_material.grand_total',
                'receive_karahi_material.paid_amount',
                'receive_karahi_material.karai_vendor_id',
                'receive_karahi_material.remaining_amount',
                'receive_karahi_material.transport_amount',
                'karai_vendor.name as karai_vendor_name'
            )
            ->where('receive_karahi_material.karai_vendor_id', $id);

            $searchValue = $request->input('search.value');

            if ($searchValue) {

                $query->where('receive_karahi_material.invoice_no', 'like', '%' . $searchValue . '%');
            }

            $totalRecords = $query->count();
            $query->orderBy('receive_karahi_material_id', 'desc');
    
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
        
        $karahiVendor = karahi::find($id);

        $karavi_available_qty = DB::table('karavi_available_qty')
            ->select('karavi_available_qty.total_qty')
            ->where('karavi_available_qty.karai_vendor_id', $id)
            ->get();
        
        if (!$karahiVendor){
            abort(404); 
        }
        
        return view('karahivendor.recdetail', compact('karahiVendor', 'karavi_available_qty'));
        
    }
 public function karahirecdetail(Request $request ,string $id ,$receive_karahi_material_id)
    {
        if($request->ajax()){

            $draw = $request->input('draw');
            
            $query = DB::table('recieve_karahi_material_detail')
            ->leftJoin('raw_material', 'recieve_karahi_material_detail.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('karai_machine', 'recieve_karahi_material_detail.karai_machine_id', '=', 'karai_machine.karai_machine_id')
            ->select(
                'recieve_karahi_material_detail.sheets',
                'recieve_karahi_material_detail.quantity',
                'recieve_karahi_material_detail.used_material_qty',
                'recieve_karahi_material_detail.used_material_cost',
                'recieve_karahi_material_detail.unit_price',
                'recieve_karahi_material_detail.total',
                'karai_machine.head_code as karai_machine_head_code',
                'raw_material.name as raw_material_name',
            )
            ->where('recieve_karahi_material_detail.receive_karahi_material_id',$receive_karahi_material_id);

            $searchValue = $request->input('search.value');

            if ($searchValue) {

                $query->where('raw_material.name', 'like', '%' . $searchValue . '%');
            }

            $totalRecords = $query->count();
            $query->orderBy('recieve_karahi_material_detail_id', 'desc');
    
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
        
        $karahiVendor = karahi::find($id);

        $karavi_available_qty = DB::table('karavi_available_qty')
            ->select('karavi_available_qty.total_qty',
                        'karavi_available_qty.total_amount')
            ->where('karavi_available_qty.karai_vendor_id', $id)
            ->first();


        $receive_karahi_material = DB::table('receive_karahi_material')
            ->select('receive_karahi_material.receive_karahi_material_id')
            ->where('receive_karahi_material.karai_vendor_id', $id)
            ->get();
        
        if (!$karahiVendor){
            abort(404); 
        }
        
        return view('karahivendor.recfulldetail', compact('karahiVendor', 'karavi_available_qty','receive_karahi_material'));
        
    }  
}
