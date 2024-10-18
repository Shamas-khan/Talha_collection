<?php
// app/Http/Controllers/CustomerController.php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use DB;
use App\Models\customerledger;
class CustomerController extends Controller
{
    public function edit(string $id)
    {
        $customer = DB::table('customer')
        ->where('customer_id',$id)
        ->first();

      
        return view('customers.edit', compact('customer'));
       
    }
    public function paymentbyid(string $id,$paymentvoucher_id)
    { 
        $supplier = DB::table('customer')
        ->where('customer_id', $id)
        ->first();

        $payment = DB::table('paymentvoucher')
            ->select(
                'paymentvoucher.narration',
                'paymentvoucher.amount as paid_amount',
                'paymentvoucher.created_at',
                'paymentvoucher.paymentvoucher_id'
            )
            ->where('paymentvoucher_id', $paymentvoucher_id)->first();

        

        return view('customers.paymentbyid',compact('supplier','payment'));


    }
    
    public function sellby_id(string $id,$sell_id)
    {
        $supplier = DB::table('customer')
        ->where('customer_id', $id)
        ->first();

        $purchase_material = DB::table('sell')
        ->leftJoin('currency', 'sell.currency_id', '=', 'currency.currency_id')
        ->select(
            'sell.sell_id',
            'sell.total_amount',
            'sell.remaining_amount',
            'sell.paid_amount',
            'sell.transport',
            'sell.currency_id',
            'sell.sell_date',
            'currency.symbol as currency_name'
            
        )
        ->where('sell.sell_id', $sell_id)->first();

        return view('customers.sellbyid',compact('supplier','purchase_material'));


    }

    public function index()
    {
        return view('customers.list');
    }

    public function ledger(string $id)
    {
        $customer = DB::table('customer')
        ->where('customer_id', $id)
        ->first();

        return view('customers.ledger',compact('customer'));
    }
 
    
 
    

public function ledgerlist(Request $request, string $id)
{
    $draw = $request->input('draw');
    $start = (int) $request->input('start', 0);
    $length = (int) $request->input('length', 10);
    $searchValue = $request->input('search.value');

  
    $query = DB::table('customer_ledger')
        ->selectRaw('
            customer_ledger_id,
            debit,
            credit,
            customer_id,
            sell_id,
            paymentvoucher_id,
            customer_payment_id,
            created_at,
            status,
            narration,
            SUM(debit - credit) OVER (ORDER BY customer_ledger_id ASC) AS running_balance
        ')
        ->where('customer_id', $id);

   
    if (!empty($searchValue)) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('narration', 'like', "%{$searchValue}%")
                ->orWhere(DB::raw('DATE(created_at)'), 'like', "%{$searchValue}%")
                ->orWhere('status', 'like', "%{$searchValue}%");
        });
    }

   
    $totalRecords = DB::table('customer_ledger')
        ->where('customer_id', $id)
        ->count();

    
    $results = $query
        ->orderBy('customer_ledger_id', 'desc')
        ->offset($start)
        ->limit($length)
        ->get();

   
    $data = [
        'draw' => (int) $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords, 
        'data' => $results,
    ];

    return response()->json($data);
}


//     public function ledgerlist(Request $request, string $id)
// {
    
//     $draw = (int) $request->input('draw');
//     $start = (int) $request->input('start', 0);
//     $length = (int) $request->input('length', 10);
//     $searchValue = $request->input('search.value');

   
//     $runningBalance = 0;

    
//     $query = CustomerLedger::where('customer_id', $id);

    
//     if ($searchValue) {
//         $query->where(function ($query) use ($searchValue) {
//             $query->where('narration', 'like', "%$searchValue%")
//                   ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%")
//                   ->orWhere('status', 'like', "%$searchValue%");
//         });
//     }

   
//     $totalRecords = $query->count();

   
//     $allRecords = $query->orderBy('customer_ledger_id', 'asc')->get();

  
//     $allRecords->transform(function ($item) use (&$runningBalance) {
//         $runningBalance += $item->debit - $item->credit; 
//         $item->running_balance = $runningBalance; 
//         return $item;
//     });

   
//     $allRecords = $allRecords->reverse();

    
//     $results = $allRecords->slice($start, $length)->values(); 

    
//     $data = [
//         'draw' => $draw,
//         'recordsTotal' => $totalRecords,
//         'recordsFiltered' => $totalRecords,
//         'data' => $results,
//     ];

    
//     return response()->json($data);
// }

    
    
    // public function ledgerlist(Request $request, string $id)
    // {
       
    //     $draw = $request->input('draw');
    //     $start = (int) $request->input('start', 0);
    //     $length = (int) $request->input('length', 10);
    //     $searchValue = $request->input('search.value');
    
    //    $query = "
    //             SELECT
    //                 result.customer_ledger_id,
    //                 result.debit,
    //                 result.credit,
    //                 result.customer_id,
    //                 result.sell_id,
    //                 result.paymentvoucher_id,
    //                 result.customer_payment_id,
    //                 result.created_at,
    //                 result.status,
    //                 result.narration,
    //                 result.running_balance
    //             FROM (
    //                 SELECT
    //                     cl.customer_ledger_id,
    //                     cl.debit,
    //                     cl.credit,
    //                     cl.customer_id,
    //                     cl.sell_id,
    //                     cl.paymentvoucher_id,
    //                     cl.customer_payment_id,
    //                     cl.created_at,
    //                     cl.status,
    //                     cl.narration,
    //                     @balance := @balance - cl.credit + cl.debit AS running_balance
    //                 FROM
    //                     (SELECT @balance := 0) AS var_init, 
    //                     customer_ledger AS cl
    //                 WHERE
    //                     cl.customer_id = {$id}
    //                 ORDER BY
    //                     cl.customer_ledger_id ASC
    //             ) AS result
    //             ORDER BY result.customer_ledger_id DESC 
    //             LIMIT {$start}, {$length}
    //         ";

    
       
    //     if ($searchValue) {
    //         $query .= " AND (result.narration LIKE '%{$searchValue}%' OR DATE(result.created_at) LIKE '%{$searchValue}%' OR result.status LIKE '%{$searchValue}%') ";
    //     }
    
       
    //     $results = DB::select($query);
    
      
    //     $totalRecords = DB::table('customer_ledger')
    //         ->where('customer_id', $id)
    //         ->when($searchValue, function ($query) use ($searchValue) {
    //             return $query->where(function ($query) use ($searchValue) {
    //                 $query->where('narration', 'like', "%$searchValue%")
    //                     ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%")
    //                     ->orWhere('status', 'like', "%$searchValue%");
    //             });
    //         })
    //         ->count();
    
        
        
    
       
    //     $data = [
    //         'draw' => (int) $draw,
    //         'recordsTotal' => $totalRecords,
    //         'recordsFiltered' => $totalRecords,
    //         'data' => $results,
    //     ];
    
       
    //     return response()->json($data);
    // }

    // public function ledgerlist(Request $request, string $id)
    // {
    //    
    //     $draw = $request->input('draw');
    //     $start = (int) $request->input('start', 0);
    //     $length = (int) $request->input('length', 10);
    //     $searchValue = $request->input('search.value');
    
    //     // Get the last balance for initialization (descending order)
    //     $lastTransaction = 0; // Initialize with 0 or fetch the last running balance for the customer if needed
    
    //     // Base query for customer ledger with subquery to calculate running balance in reverse
    //     $query = "
    //         SELECT
    //             result.customer_ledger_id,
    //             result.debit,
    //             result.credit,
    //             result.customer_id,
    //             result.sell_id,
    //             result.paymentvoucher_id,
    //             result.customer_payment_id,
    //             result.created_at,
    //             result.status,
    //             result.narration,
    //             result.running_balance,
    //             result.balance_type
    //         FROM (
    //             SELECT
    //                 bl.customer_ledger_id,
    //                 bl.debit,
    //                 bl.credit,
    //                 bl.customer_id,
    //                 bl.sell_id,
    //                 bl.paymentvoucher_id,
    //                 bl.customer_payment_id,
    //                 bl.created_at,
    //                 bl.status,
    //                 bl.narration,
    //                  @balance := @balance - bl.credit + bl.debit AS running_balance,
    //                 CASE 
    //                     WHEN bl.credit > 0 THEN 'Cr'
    //                     WHEN bl.debit > 0 THEN 'Dr'
    //                     ELSE ''
    //                 END AS balance_type
    //             FROM
    //                 (SELECT @balance := {$lastTransaction}) AS var_init, -- Initialize balance
    //                 customer_ledger AS bl
    //             WHERE
    //                 bl.customer_id = {$id}
    //             ORDER BY
    //                 bl.customer_ledger_id ASC -- Important: Process in ascending order to calculate running balance
    //         ) AS result
    //         ORDER BY result.customer_ledger_id DESC -- Now reverse the order for display
    //         LIMIT {$start}, {$length}
    //     ";
    
    //     // Add search filters if search value is provided
    //     if ($searchValue) {
    //         $query .= " AND (result.narration LIKE '%{$searchValue}%' OR DATE(result.created_at) LIKE '%{$searchValue}%' OR result.status LIKE '%{$searchValue}%') ";
    //     }
    
    //     // Execute the query
    //     $results = DB::select($query);
    
    //     // Calculate total records (with or without search)
    //     $totalRecords = DB::table('customer_ledger')
    //         ->where('customer_id', $id)
    //         ->when($searchValue, function ($query) use ($searchValue) {
    //             return $query->where(function ($query) use ($searchValue) {
    //                 $query->where('narration', 'like', "%$searchValue%")
    //                     ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%")
    //                     ->orWhere('status', 'like', "%$searchValue%");
    //             });
    //         })
    //         ->count();
    
    //     // Add balance type (CR/DR) to the running balance
    //     foreach ($results as &$result) {
    //         $result->running_balance .= ' ' . $result->balance_type;
    //     }
    
    //     // Prepare response data
    //     $data = [
    //         'draw' => (int) $draw,
    //         'recordsTotal' => $totalRecords,
    //         'recordsFiltered' => $totalRecords,
    //         'data' => $results,
    //     ];
    
    //     // Return JSON response
    //     return response()->json($data);
    // }
    


    


    
    
    
    


    public function paymentlist(Request $request,string $id)
    {
        if($request->ajax()){
            $draw = $request->input('draw');
            
            $query = DB::table('customer_payment')
            ->select(
                'customer_payment.narration',
                'customer_payment.paid_amount',
                'customer_payment.created_at'
            )
            ->where('customer_payment.customer_id', $id);

            $searchValue = $request->input('search.value');

            if ($searchValue) {

                $query->where('customer_payment.created_at', 'like', '%' . $searchValue . '%');
            }

            $totalRecords = $query->count();
            $query->orderBy('customer_payment_id', 'desc');

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
    public function paymentview(string $id)
    {
        $detail=  DB::table('customer')
       ->select(
            'customer.customer_id',
            'customer.company',
            'customer.name',
            'customer.total_amount',
            'customer.paid_amount',
            'customer.remaining_amount',
            
        )->where('customer.customer_id',$id)
        ->first();
        return view('customers.payment',compact('detail'));
    }
    public function selldetail(string $id,$sellid)
    {
        $sell_id=$sellid;
        $detail=  DB::table('customer')
       ->select(
            'customer.customer_id',
            'customer.company',
            'customer.name',
            'customer.total_amount',
            'customer.paid_amount',
            'customer.remaining_amount',
            
        )->where('customer.customer_id',$id)
        ->first();
        return view('customers.selldetail',compact('detail','sellid'));
    }
    public function detail(string $id)
    {
    
      $detail=  DB::table('customer')
       ->select(
            'customer.customer_id',
            'customer.company',
            'customer.name',
            'customer.total_amount',
            'customer.paid_amount',
            'customer.remaining_amount',
            
        )->where('customer.customer_id',$id)
        ->first();
        return view('customers.sell',compact('detail'));
}



public function detaillist(Request $request, string $id)
{
    if ($request->ajax()) {
        $draw = $request->input('draw');

        $query = DB::table('sell')
            ->leftJoin('customer', 'sell.customer_id', '=', 'customer.customer_id')
            ->leftJoin('currency', 'sell.currency_id', '=', 'currency.currency_id')
            ->select(
                'sell.sell_id',
                DB::raw("CONCAT('INO-00', sell.sell_id) as invoice_sell_id"), 
                'sell.customer_id',
                'sell.total_amount',
                'sell.paid_amount',
                'sell.remaining_amount',
                'sell.transport',
                'sell.sell_date',
                'customer.name as customer_name',
                'customer.total_amount as customer_total_amount',
                'customer.paid_amount as customer_paid_amount',
                'customer.remaining_amount as customer_remaining_amount',
                'currency.symbol as currency_name'
            )->where('sell.customer_id', $id);

        $searchValue = $request->input('search.value');

        if ($searchValue) {
            $query->where('customer.name', 'like', '%' . $searchValue . '%');
        }

        $totalRecords = $query->count();
        $query->orderBy('sell.sell_id', 'desc');

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
public function selldetaillist(Request $request, string $id,$sellid)
{
    
    if ($request->ajax()) {
        $draw = $request->input('draw');

        $query = DB::table('sell_detail')
            ->leftJoin('finish_product', 'sell_detail.finish_product_id', '=', 'finish_product.finish_product_id')
            ->select(
                'sell_detail.sell_detail_id',
                'sell_detail.order_product_qty',
                'sell_detail.unit_price',
                'sell_detail.total_price',
               
               'finish_product.product_name as product_name',
              
            )->where('sell_detail.sell_id', $sellid);

        $searchValue = $request->input('search.value');

        if ($searchValue) {
            $query->where('finish_product.product_name', 'like', '%' . $searchValue . '%');
        }

        $totalRecords = $query->count();
        $query->orderBy('sell_detail.sell_detail_id', 'desc');

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
    $query = Customer::query();

    
    $query->orderBy('customer_id', 'desc');

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


    public function create()
    {
        return view('customers.add');
    }

    public function store(StoreCustomerRequest $request)
{
    DB::beginTransaction();
    try {


        $transactionType = $request->input('transaction_type');
        $debit = 0;
        $credit = 0;

        if ($transactionType === 'debit') {
            $debit = $request->input('op_balance');
        
        $customer_id = DB::table('customer')->insertGetId([
            'name' => $request->input('name'),
            'company' => $request->input('company'),
            'contact' => $request->input('contact'),
            'address' => $request->input('address'),
            'op_balance' => $debit,
            'total_amount' => $debit,
            'remaining_amount' => $debit,
        ]);
    } elseif ($transactionType === 'credit') {
        $credit = $request->input('op_balance');
        $customer_id = DB::table('customer')->insertGetId([
            'name' => $request->input('name'),
            'company' => $request->input('company'),
            'contact' => $request->input('contact'),
            'address' => $request->input('address'),
            'op_balance' => -$request->input('op_balance'),
            'total_amount' => -$request->input('op_balance'),
            'remaining_amount' => -$request->input('op_balance'),
        ]);
    }


        if (!$customer_id) {
            throw new \Exception('Customer ID not generated');
        }

       
        DB::table('customer_ledger')->insert([
            'customer_id' => $customer_id,
            'status' => 'Opening',
            'narration' => 'Opening',
             'debit' => $debit,
            'credit' => $credit,
            'running_balance' => $request->input('op_balance'),
            
        ]);

        DB::commit();

        session()->flash('success', 'Customer added successfully!');
        return redirect('customers');
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'An error occurred ->' . $e->getMessage());
        return back()->withInput();
    }
}


    public function show(Customer $Customer)
    {
        $data=CustomerResource::collection(Customer::all());
        dd($data);
        return view('customers.list',$data);
    }

public function update(Request $request, $id)
{
    DB::beginTransaction();
    try {
        // Validate the incoming request
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

        // Existing customer data fetch karein
        $existingCustomer = DB::table('customer')->where('customer_id', $id)->first();
        $existing_op_balance = $existingCustomer->op_balance;


        $debitsum = DB::table('customer_ledger')
                ->where('customer_id', $id)
                ->where(function($query) {
                    $query->where('status', 'Sell')
                          ->orWhere('status', 'return');
                })
                ->sum('debit');


        

        // Existing received payments ko fetch karein
        $creditsum = DB::table('customer_ledger')
            ->where('customer_id', $id)
            ->where('status', 'Recieved')
           ->sum('credit');  // Total received payments

        

        // Difference nikalain op_balance ka
        $balance_difference = $new_op_balance - $existing_op_balance;

        if ($transactionType === 'debit') {
            $debit = $new_op_balance;
        
            // Total and remaining amount ko properly adjust karein
            $total_amount = ($debitsum - $creditsum) + $new_op_balance;
            $remaining_amount = $total_amount;
        
            // Update customer details
            DB::table('customer')->where('customer_id', $id)->update([
                'name' => $request->input('name'),
                'company' => $request->input('company'),
                'contact' => $request->input('contact'),
                'address' => $request->input('address'),
                'op_balance' => $debit,
                'total_amount' => $total_amount,
                'remaining_amount' => $remaining_amount,
            ]);
        }
        elseif ($transactionType === 'credit') {
            $credit = $new_op_balance;
        
            // Total and remaining amount ko adjust karein
            $total_amount = ($debitsum - $creditsum) - $new_op_balance;
            $remaining_amount = $total_amount;
        
            // Update customer details
            DB::table('customer')->where('customer_id', $id)->update([
                'name' => $request->input('name'),
                'company' => $request->input('company'),
                'contact' => $request->input('contact'),
                'address' => $request->input('address'),
                'op_balance' => -$credit,
                'total_amount' => $total_amount,
                'remaining_amount' => $remaining_amount,
            ]);
        }
        

        // Ledger me bhi balance update karein
        DB::table('customer_ledger')
    ->where('customer_id', $id)
    ->where('status', 'Opening')
    ->update([
        'debit' => $debit,
        'credit' => $credit,
        'running_balance' => 0, // Updated running balance
    ]);
        DB::commit();

        session()->flash('success', 'Customer updated successfully!');
        return redirect('customers');
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'An error occurred ->' . $e->getMessage());
        return back()->withInput();
    }
}




    public function destroy(Customer $Customer)
    {
        $Customer->delete();
        $data=CustomerResource::collection(Customer::all());
        return view('customers.list');
    }

    public function customerpaymentstore(Request $request)
    {

        DB::beginTransaction();
        try{

            $customer = Customer::findOrFail($request->customer_id);
            $runningBalance = $customer->remaining_amount;
       
         
            $payment_id = DB::table('customer_payment')->insertGetId([
                'customer_id' => $request->customer_id,
                'narration' => $request->narration,
                'paid_amount' => $request->amount,
            ]);

            DB::table('customer_ledger')->insert([
                'customer_id' => $request->customer_id,
                'customer_payment_id' => $payment_id,
                'status' => 'Payment',
                'narration' => $request->narration,
                'credit' => $request->amount,
                'running_balance' => $runningBalance - $request->amount, // Update running_balance
            ]);

            $customer->remaining_amount = $runningBalance - $request->amount;
            $customer->paid_amount +=$request->amount;
            $customer->save();
            
            DB::commit();
            session()->flash('success', 'Payment Added successfully!');
            return redirect()->back();
        }
        catch (\Throwable $th) {
            
            DB::rollBack();
            session()->flash('error', $th->getMessage());
            return redirect()->back();
        }
    }
}
