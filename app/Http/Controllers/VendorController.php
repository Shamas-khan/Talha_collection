<?php

namespace App\Http\Controllers;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Requests\VendorReq;
use App\Http\Resources\VendorResource;
use DB;

class VendorController extends Controller
{
    public function issuebyid(string $id,$issue_id)
    {
        $supplier = DB::table('vendor')
        ->where('vendor_id', $id)
        ->first();


        $purchase_material = DB::table('issue_material')
        ->leftJoin('customer', 'issue_material.customer_id', '=', 'customer.customer_id')
        ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
        ->select(
            'issue_material.created_at',
            'issue_material.customer_id',
            'issue_material.issue_material_id',
            'issue_material.total_amount',
            'issue_material.paid_amount',
            'issue_material.remaining_amount',
            'customer.name as customer_name',
          
            
            'finish_product.product_name',
           
        )->where('issue_material.issue_material_id', $issue_id)->first();

        return view('vendor.issuebyid',compact('supplier','purchase_material'));


    }

    public function paymentbyid(string $id,$paymentvoucher_id)
    {
        $supplier = DB::table('vendor')
        ->where('vendor_id', $id)
        ->first();

        $payment = DB::table('paymentvoucher')
            ->select(
                'paymentvoucher.narration',
                'paymentvoucher.amount as paid_amount',
                'paymentvoucher.created_at',
                'paymentvoucher.paymentvoucher_id'
            )
            ->where('paymentvoucher_id', $paymentvoucher_id)->first();

        

        return view('vendor.paymentbyid',compact('supplier','payment'));


    }
    public function ledger(string $id)
    {
        $vendor = DB::table('vendor')
        ->where('vendor_id', $id)
        ->first();

        return view('vendor.ledger',compact('vendor'));
    }

    // public function ledgerlist(Request $request,string $id)
    // {
    //     $draw = $request->input('draw');
        
    //     $query = DB::table('vendor_ledger')
    //     ->select(
            
    //         'vendor_ledger.running_balance',
    //         'vendor_ledger.debit',
    //         'vendor_ledger.credit',
    //         'vendor_ledger.issue_material_id',
    //         'vendor_ledger.paymentvoucher_id',
            
    //         'vendor_ledger.created_at',
           
    //         'vendor_ledger.status',
    //         'vendor_ledger.narration',
    //         'vendor_ledger.vendor_id',
            
    //     )
    //     ->where('vendor_ledger.vendor_id', $id);
        
        
       
    //     $query->orderBy('vendor_ledger.vendor_ledger_id', 'desc');
        
       
    //     $searchValue = $request->input('search.value');
    //     if (!empty($searchValue)) {
    //         $query->where(function ($query) use ($searchValue) {
    //             $query->where('vendor_ledger.created_at', 'like', "%$searchValue%");
    //                 // ->orWhere('purchase_material.purchase_material_id', 'like', "%$searchValue%");
    //             // Add more search conditions for other columns if needed
    //         });
    //     }
        
        
    //     $totalRecords = $query->count();
        
       
    //     $start = $request->input('start', 0);
    //     $length = $request->input('length', 10);
    //     $query->offset($start)->limit($length);
        
       
    //     $results = $query->get();
        
       
    //     $data = [
    //         'draw' => (int) $draw,
    //         'recordsTotal' => $totalRecords, 
    //         'recordsFiltered' => $totalRecords, 
    //          'data' => $results,
    //     ];
        
       
    //     return response()->json($data);
    // }


    public function ledgerlist(Request $request, string $id)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value');
        $page = $start / $length;
    
       
        $query = "
            WITH RunningBalance AS (
                SELECT
                    bl.vendor_ledger_id,
                    bl.debit,
                    bl.credit,
                    bl.vendor_id,
                    bl.issue_material_id,
                    bl.paymentvoucher_id,
                    bl.created_at,
                    bl.status,
                    bl.narration,
                    @balance := @balance + bl.credit - bl.debit AS running_balance
                
                FROM
                    (SELECT @balance := ?) AS var_init,
                    vendor_ledger AS bl
                WHERE
                    bl.vendor_id = ?
                    " . ($searchValue ? "AND (bl.narration LIKE ? OR DATE(bl.created_at) LIKE ?)" : "") . "
                ORDER BY
                    bl.vendor_ledger_id ASC
            )
            SELECT *
            FROM RunningBalance
            ORDER BY vendor_ledger_id DESC
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
        $totalRecords = DB::table('vendor_ledger')
            ->where('vendor_id', $id)
            ->when($searchValue, function ($query, $searchValue) {
                return $query->where(function ($query) use ($searchValue) {
                    $query->where('narration', 'like', "%$searchValue%")
                        ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%");
                });
            })
            ->count();

           
    
       
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
        return view('vendor.list');
    }

    
    public function create()
    {
        return view('vendor.add');
    }

    public function store(VendorReq $request)
    {
        DB::beginTransaction();
        try {   


            $transactionType = $request->input('transaction_type');
        $debit = 0;
        $credit = 0;

        if ($transactionType === 'debit') {
            $debit = $request->input('op_balance');


            $vendor = Vendor::create([
                'name' => $request->input('name'),
                'contact' => $request->input('contact'),
                'cnic' => $request->input('cnic'),
                'address' => $request->input('address'),
                'op_balance' => -$debit,
                'total_amount' => -$debit,
                'remaining_amount' => -$debit,
            ]);


        }
        elseif ($transactionType === 'credit') {
            $credit = $request->input('op_balance');

            $vendor = Vendor::create([
                'name' => $request->input('name'),
                'contact' => $request->input('contact'),
                'cnic' => $request->input('cnic'),
                'address' => $request->input('address'),
                'op_balance' => $request->input('op_balance'),
                'total_amount' => $request->input('op_balance'),
                'remaining_amount' => $request->input('op_balance')
            ]);

        }

                          
                            if (!$vendor) {
                                throw new \Exception(' vendor creation failed');
                            }

                            DB::table('vendor_ledger')->insert([
                                'vendor_id' => $vendor->vendor_id,
                                'status' => 'Opening',
                                'narration' => 'Opening',
                                'debit' => $debit,
                                'credit' => $credit,
                            'running_balance' => $request->input('op_balance'),
                        ]);
                DB::commit();
                        
                        session()->flash('success', 'Vendor created successfully!');
                        return redirect('vendors');
        }
        catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred -> ' . $e->getMessage());
            return back()->withInput();
        }

    }

    public function listing(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder
        $query = Vendor::query();
    
        // Apply sorting
        // $sortColumnIndex = $request->input('order.0.column');
        // $sortDirection = $request->input('order.0.dir');
        // $sortColumnName = $request->input("columns.$sortColumnIndex.data");
        // $query->orderBy($sortColumnName, $sortDirection);

        $query->orderBy('vendor_id', 'desc');
        // Apply searching
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")
                ->orWhere('vendor_id', 'like', "%$searchValue%");
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
        $vendor = DB::table('vendor')
        ->where('vendor_id',$id)
        ->first();

      
        return view('vendor.edit', compact('vendor'));
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendorReq $request, $id)
    {
        DB::beginTransaction();
        try {
            $transactionType = $request->input('transaction_type');
            $new_op_balance = str_replace(',', '', $request->input('op_balance'));
    
            // Pehle existing op_balance ko fetch karein
            $existingVendor = Vendor::findOrFail($id);
            $existing_op_balance = $existingVendor->op_balance;
    
            // Total debit aur credit ko sum karein
            $debitsum = DB::table('vendor_ledger')
                ->where('vendor_id', $id)
                
                ->where('status', 'return')
               
                ->sum('debit');
    
                $creditsum = DB::table('vendor_ledger')
                ->where('vendor_id', $id)
                ->where(function($query) {
                    $query->where('status', 'Production')
                          ->orWhere('status', 'Payment');
                })
                ->sum('credit');
   
            // Balance ka difference calculate karein
            $balance_difference = $new_op_balance - $existing_op_balance;
    
            $total_amount = 0;
            $remaining_amount = 0;
    
            if ($transactionType === 'debit') {
                $debit = $new_op_balance;
     
                // Total and remaining amount calculation for debit
                $total_amount = ($debitsum - $creditsum) + $new_op_balance;
                $remaining_amount = -$total_amount; // Remaining balance negative hoga for debit
    
                // Update vendor with debit transaction
                $existingVendor->update([
                    'name' => $request->input('name'),
                    'contact' => $request->input('contact'),
                    'cnic' => $request->input('cnic'),
                    'address' => $request->input('address'),
                    'op_balance' => -$debit,
                    'total_amount' => -$total_amount,
                    'remaining_amount' => $remaining_amount,
                ]);
            } elseif ($transactionType === 'credit') {
                $credit = $new_op_balance;
    
                // Total and remaining amount calculation for credit
                $total_amount = ($creditsum - $debitsum) + $new_op_balance;
                $remaining_amount = $total_amount; // Remaining balance positive hoga for credit
    
                // Update vendor with credit transaction
                $existingVendor->update([
                    'name' => $request->input('name'),
                    'contact' => $request->input('contact'),
                    'cnic' => $request->input('cnic'),
                    'address' => $request->input('address'),
                    'op_balance' => $credit,
                    'total_amount' => $total_amount,
                    'remaining_amount' => $remaining_amount,
                ]);
            }
    
            // Ledger table ko bhi update karein
            DB::table('vendor_ledger')
                ->where('vendor_id', $id)
                ->where('status', 'Opening')
                ->update([
                    'debit' => $transactionType === 'debit' ? $debit : 0,
                    'credit' => $transactionType === 'credit' ? $credit : 0,
                    'running_balance' => $remaining_amount, // Running balance ko remaining amount se sync karein
                ]);
    
            DB::commit();
    
            session()->flash('success', 'Vendor updated successfully!');
            return redirect('vendors');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred -> ' . $e->getMessage());
            return back()->withInput();
        }
    }
    
    

    public function vendorpaymentstore(Request $request)
    {
          // Payment ko karahi_vendor_payment table mein insert karna
          DB::table('vendor_payment')->insert([
            'vendor_id' => $request->vendor_id,
            'narration' => $request->narration, 
            'paid_amount' => $request->amount,
        ]);
    
        // karai_vendor table ko update karna
        $vendor = Vendor::find($request->vendor_id);
        $vendor->paid_amount += $request->amount;
        $vendor->remaining_amount -= $request->amount;
        $vendor->save();
    
        session()->flash('success', 'Payment Added successfully!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function vendorpayment(Request $request,string $id)
    {
        if($request->ajax()){
            $draw = $request->input('draw');
            
            $query = DB::table('vendor_payment')
            ->leftJoin('vendor', 'vendor_payment.vendor_id', '=', 'vendor.vendor_id')
            ->select(
                'vendor_payment.narration',
                'vendor_payment.paid_amount',
                'vendor_payment.created_at',
                'vendor.name as vendor_name'
            )
            ->where('vendor.vendor_id', $id);

            $searchValue = $request->input('search.value');

            if ($searchValue) {

                $query->where('vendor.name', 'like', '%' . $searchValue . '%');
            }

            $totalRecords = $query->count();
            $query->orderBy('vendor_payment_id', 'desc');

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

        $vendor = Vendor::findOrFail($id);
        return view('vendor.payment', compact('vendor'));
    }
    public function vendordetail(Request $request ,string $id)
    {
        if($request->ajax()){
            $draw = $request->input('draw');
    
        
            $query = DB::table('issue_material')
            ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
            ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
            ->select(
                'issue_material.issue_material_id',
                'issue_material.total_quantity',
                'issue_material.received_quantity',
                'issue_material.remaining_quantity',
                'issue_material.total_amount',
                'issue_material.paid_amount',
                'issue_material.remaining_amount',
                'vendor.name as vendor_name',
                'vendor.vendor_id as vendor_id',
                'finish_product.product_name',
                'finish_product.finish_product_id as finish_product_id',
            )->where('issue_material.vendor_id', $id);
        
            
            $query->orderBy('issue_material.issue_material_id', 'desc');
           
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->where(function ($query) use ($searchValue) {
                    $query->where('name', 'like', "%$searchValue%");
                    
                });
            }
        
            
            $totalRecords = $query->count();
        
          
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $query->offset($start)->limit($length);
        
            
            $customers = $query->get();
        
         
            $data = [
                'draw' => (int)$draw,
                'recordsTotal' => $totalRecords, 
                'recordsFiltered' => $totalRecords, 
                'data' => $customers,
            ];
         
           
            return response()->json($data);
        }


        $vendor = Vendor::findOrFail($id);
        return view('vendor.detail', compact('vendor'));
    }
}
