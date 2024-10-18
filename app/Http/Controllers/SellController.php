<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use App\Models\RawMaterial;
use App\Models\FinishProduct;
use Illuminate\Http\Request;
use App\Http\Requests\SellRequest;
use DB;
class SellController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function builty(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'builty_number' => 'required',
                'transport' => 'required',
                'sell_id' => 'required', // Ensure sell_id exists in the sell table
            ]);
    
            // Check if the builty number already exists
            $existingBuilty = DB::table('sell')
                ->where('sell_id', $validatedData['sell_id'])
                ->where('builty_nbr', $validatedData['builty_number'])
                ->first();
    
            if ($existingBuilty) {
                // Builty number already exists
                session()->flash('error', 'Builty number already exists.');
                return redirect()->back()->withInput();
            }
    
            // Update the record in the database
            $updateCount = DB::table('sell')
                ->where('sell_id', $validatedData['sell_id'])
                ->update([
                    'builty_nbr' => $validatedData['builty_number'],
                    'transport' => $validatedData['transport'],
                ]);
    
            // Check if any row was updated
            if ($updateCount === 0) {
                // No rows were updated
                return redirect()->back()->withErrors(['update' => 'No record found to update.']);
            }
    
            // Flash success message to the session
            session()->flash('success', 'Builty number updated successfully.');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exception
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Handle any other exceptions
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred.'])->withInput();
        }
    
        // Redirect back to the previous page
        return redirect()->back();
    }
    

    public function index()
    {
       
        return view('sell.list');
    }
    public function newsellstore(Request $request)
{
  
    $validatedData = $request->validate([
        'sell_date' => 'required',
        'unit_price.*' => 'required',
        'transport' => 'nullable|numeric',
        // 'paid_amount' => 'required',
        'currency_id' => 'required',
        'sale_stock' => 'required',
        // 'narration' => 'required',
        'total_cost' => 'required',
    ]);

    DB::beginTransaction();

    try {
        $sell_date = $validatedData['sell_date'];
        $transport = $validatedData['transport'];
        $paid_amount = 0;
        $currency_id = $validatedData['currency_id'];
        $unit_price = $validatedData['unit_price'];
        $narration = 0;
        $total_cost = $validatedData['total_cost'];
        $grand_total = $request->input('grand_total');
        $sell_order_id = $request->input('sell_order_id');
        $customer_id = $request->input('customer_id');
        $finish_product_id = $request->input('finish_product_id');
        $order_quantity = $request->input('order_quantity');
        $total_amount = $request->input('total_amount');
        $sale_stock = $request->input('sale_stock');

        $remainingAmount = $grand_total - $paid_amount;

        if ($paid_amount > $grand_total) {
            throw new \Exception('Paid amount must be equal to or less than total amount.');
        }

        if ($paid_amount > 0) {
            DB::table('customer_payment')->insert([
                'customer_id' => $customer_id,
                'narration' => $narration,
                'paid_amount' => $paid_amount,
            ]);
        }

        $sell_data = [
            'sell_order_id' => $sell_order_id,
            'customer_id' => $customer_id,
            'total_amount' => $grand_total,
            'paid_amount' => $paid_amount,
            'remaining_amount' => $remainingAmount,
            'transport' => $transport,
            'currency_id' => $currency_id,
            'sell_date' => $sell_date,
        ];

        $sell_id = DB::table('sell')->insertGetId($sell_data);
        $grandproductiontotal = 0;

        $finish_products = DB::table('finish_product')
            ->whereIn('finish_product_id', $finish_product_id)
            ->leftJoin('raw_material as small_shoper', 'finish_product.small_shoper_id', '=', 'small_shoper.raw_material_id')
            ->leftJoin('raw_material as big_shoper', 'finish_product.big_shoper_id', '=', 'big_shoper.raw_material_id')
            ->select(
                'finish_product.finish_product_id',
                'finish_product.small_shoper_id',
                'finish_product.small_product_qty',
                'finish_product.big_shoper_id',
                'finish_product.big_product_qty',
                'small_shoper.name as small_shoper_name',
                'big_shoper.name as big_shoper_name'
            )
            ->get()
            ->keyBy('finish_product_id');

        foreach ($finish_product_id as $i => $fdid) {
            $shoper = $finish_products->get($fdid);

            if (!$shoper) {
                throw new \Exception("Finish product not found for ID $fdid");
            }

            $newsmall = ceil($order_quantity[$i] / $shoper->small_product_qty);
            $newbig = ceil($order_quantity[$i] / $shoper->big_product_qty);

            $smallshoperprice = $this->finalPrice($shoper->small_shoper_id, $newsmall);
            $bigshoperprice = $this->finalPrice($shoper->big_shoper_id, $newbig);
             $this->updateqtyForRaw($fdid,$order_quantity[$i]);

          

            $this->reduceQty($shoper->small_shoper_id, $newsmall);
            $this->reduceQty($shoper->big_shoper_id, $newbig);

            $shopernewprice = $smallshoperprice + $bigshoperprice;
            
            $newtotaldivide = (float)$total_cost[$i] + (float)$shopernewprice;

            


            // $newtotaldivide = (float)$total_cost[$i] / (float)$order_quantity[$i];

            // $new_total_cost = (float)$newtotaldivide + (float)$shopernewprice;

            

            $perpiececost = $newtotaldivide / $order_quantity[$i];

           

            $order_qty_dozen = $order_quantity[$i] / 12;
            $updateorder_qty_dozen = number_format($order_qty_dozen, 1);

            $sell_detail = [
                'sell_id' => $sell_id,
                'finish_product_id' => $finish_product_id[$i],
                'order_product_qty' => $order_quantity[$i],
                'unit_price' => $unit_price[$i],
                'total_price' => $total_amount[$i],
                'production_total_cost' => $newtotaldivide,
                'production_piece_cost' => $perpiececost,
                'order_qty_dozen' => $updateorder_qty_dozen,
            ]; 

           
           
            $grandproductiontotal += $newtotaldivide;

            DB::table('sell_detail')->insert($sell_detail);

            if ($sale_stock[$i] === "old") {
            
                $current_quantity = DB::table('old_fproduct_stock')
                    ->where('finish_product_id', $fdid)
                    ->value('quantity');
            
                if ($current_quantity === 0) {
                    throw new \Exception('Current quantity not found');
                }

                $new_quantity = $current_quantity - $order_quantity[$i];

                DB::table('old_fproduct_stock')
                    ->where('finish_product_id', $fdid)
                    ->update(['quantity' => $new_quantity]);


            }
            else{
                    $current_quantity = DB::table('finish_product_stock')
                    ->where('finish_product_id', $fdid)
                    ->value('quantity');
            
                if ($current_quantity === 0) {
                    throw new \Exception('Current quantity not found');
                }

                $new_quantity = $current_quantity - $order_quantity[$i];

                DB::table('finish_product_stock')
                    ->where('finish_product_id', $fdid)
                    ->update(['quantity' => $new_quantity]);
            }

            
        }

        $newgrandtotal = ceil($grandproductiontotal);
        DB::table('sell')
            ->where('sell_id', $sell_id)
            ->update(['grand_production_cost' => $newgrandtotal]);

        $currentValues = DB::table('customer')
            ->select('remaining_amount', 'total_amount', 'paid_amount')
            ->where('customer_id', $customer_id)
            ->first();

        if ($currentValues) {
            $newPaidAmount = $currentValues->paid_amount + $paid_amount;
            $newRemainingAmount = $currentValues->remaining_amount + $remainingAmount;
            $total_amount = $currentValues->total_amount + $grand_total;

            DB::table('customer')
                ->where('customer_id', $customer_id)
                ->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                    'total_amount' => $total_amount,
                ]);

                DB::table('customer_ledger')->insert([
                    'customer_id' => $customer_id,
                    'status' => 'sell',
                    'narration' => 'sell',
                    'debit' => $grand_total,
                    'running_balance' => $newRemainingAmount, // Update running_balance
                    'sell_id' => $sell_id, 
                ]);
        }

        DB::table('sell_order')
            ->where('sell_order_id', $sell_order_id)
            ->update(['status' => 2]);

        
            // DB::rollBack();
        // session()->flash('success', 'Sell order created successfully!');
        // return redirect()->route('sell.index'); 
        DB::commit();
       
        return redirect()->route('sell.prints', ['sell_id' => $sell_id]);

           
        
       
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'An error occurred: ' . $e->getMessage());
        return redirect()->back();
    }
}

public function print($sell_id) {

    $aggregatedData = DB::table('sell')
    ->leftJoin('sell_detail', 'sell.sell_id', '=', 'sell_detail.sell_id')
    ->leftJoin('customer', 'sell.customer_id', '=', 'customer.customer_id')
    ->leftJoin('currency', 'sell.currency_id', '=', 'currency.currency_id')
    ->leftJoin('finish_product', 'sell_detail.finish_product_id', '=', 'finish_product.finish_product_id')
    ->select(
    'sell_detail.sell_detail_id',
    'sell_detail.order_qty_dozen',
    'sell_detail.order_product_qty',
    'sell_detail.unit_price',
    'sell_detail.total_price',
    'sell_detail.production_total_cost',
    'sell_detail.production_piece_cost',
    'customer.name as customer_name',
    'finish_product.product_name',
   'sell.total_amount as total_amount',
   'sell.sell_id as sell_id',
   'sell.paid_amount as paid_amount',
   'sell.remaining_amount as remaining_amount',
   'sell.transport as transport',
   'sell.sell_date as sell_date',
   'sell_detail.finish_product_id as pid',
    'currency.symbol as currency_symbol',
    )
    ->where('sell.sell_id', $sell_id)
    ->get();
    
    $data = [];

    foreach ($aggregatedData as $d) {
        $pid = $d->pid;
    
        // Check if the product (pid) is already in the array
        if (isset($data[$pid])) {
            // Add values to the existing entry
            $data[$pid]->order_qty_dozen += $d->order_qty_dozen;
            $data[$pid]->order_product_qty += $d->order_product_qty;
            $data[$pid]->total_price += $d->total_price;
            $data[$pid]->production_total_cost += $d->production_total_cost;
            $data[$pid]->unit_price += $d->unit_price;
        } else {
            // Initialize a new entry for the product (pid)
            $data[$pid] = clone $d;
        }
    }
    
    // Reset the array keys to be sequential and convert to an array of objects
    $data = array_values($data);
   
    return  view('print.sellinvoice',compact('data'));

}


public function updateqtyForRaw($fpid, $qty) 
{
    // Sirf finish product rows lein jahan calculation set hai aur greater than 0 hai
    $raw = DB::table('issue_material')
        ->where('finished_product_id', $fpid)
        ->whereNotNull('calculation')
        ->where('calculation', '>', 0)
        ->select('issue_material.calculation as remaining_qty', 'issue_material.issue_material_id as pid')
        ->get();

    foreach ($raw as $r) {
        if ($qty <= 0) {
            break; // Agar qty 0 ho gayi hai toh loop stop karo
        }

        if ($r->remaining_qty >= $qty) {
            $q = $r->remaining_qty - $qty;
            $this->updateRemaining($q, $r->pid);
            break; // Agar required qty puri ho gayi hai toh loop stop karo
        } else {
            $qty -= $r->remaining_qty;
            $this->updateRemaining(0, $r->pid); // remaining_qty 0 set kardo
        }
    }
}

public function updateRemaining($q, $pid)
{
    // Calculation field ko update karo sirf given pid ke liye
    DB::table('issue_material')
        ->where('issue_material_id', $pid)
        ->update([
            'calculation' => $q
        ]);
}



// public function updateqtyForRaw($fpid, $qty) 
// {

//     $raw = DB::table('issue_material')
//         ->where('finished_product_id', $fpid)
//         ->whereNotNull('calculation')
//         ->where('calculation', '>', 0)
//         ->select('issue_material.calculation as remaining_qty', 'issue_material.issue_material_id as pid')
//         ->get();

//     foreach ($raw as $r) {
//         if ($r->remaining_qty >= $qty) {

//             $q = $r->remaining_qty - $qty;

//             $this->updateRemaining($q, $r->pid);

//         } else {
//             $q = $r->remaining_qty - $r->remaining_qty;
//             $qty -= $r->remaining_qty;

//             $this->updateRemaining($q, $r->pid);
//         }
//     }


// }
// public function updateRemaining($q, $pid)
// {

//     DB::table('issue_material')
//         ->where('issue_material_id', $pid)
//         ->update([
//             'calculation' => $q
//         ]);
// }
    
   
    public function create()
    {
        $customer=Customer::all();
        $fproduct=FinishProduct::all();
        return view('sell.add',compact('customer','fproduct'));
    }

    public function finalPrice($rid, $qty)
{
    $total_amount = 0;
    $raw = DB::table('purchase_material_detail')
        ->where('raw_material_id', $rid)
        ->whereNotNull('remaining_qty')
        ->where('remaining_qty', '>', 0)
        ->select('remaining_qty', 'purchase_material_detail_id as pid', 'convert_price as up')
        ->get();

    foreach ($raw as $r) {
        if ($r->remaining_qty >= $qty) {
            $amount = $r->up * $qty;
            $total_amount += $amount;

            DB::table('purchase_material_detail')
                ->where('purchase_material_detail_id', $r->pid)
                ->update(['remaining_qty' => $r->remaining_qty - $qty]);

            return $total_amount;
        } else {
            $amount = $r->up * $r->remaining_qty;
            $total_amount += $amount;

            DB::table('purchase_material_detail')
                ->where('purchase_material_detail_id', $r->pid)
                ->update(['remaining_qty' => 0]);

            $qty -= $r->remaining_qty;
        }
    }

    return $total_amount;
}
    public function reduceQty($id, $qty)
{
    $raw = DB::table('raw_stock')
        ->where('raw_material_id', $id)
        ->select('available_quantity')
        ->first();

    if (!$raw) {
        throw new \Exception("Raw stock not found for ID $id");
    }

    $fqty = $raw->available_quantity - $qty;

    $updated = DB::table('raw_stock')
        ->where('raw_material_id', $id)
        ->update(['available_quantity' => $fqty]);

    if (!$updated) {
        throw new \Exception("Failed to update raw stock for ID $id");
    }

    return true;
}

  
    public function newsell(string $id)
    {
        // Fetch sell order data
        $sell_order = DB::table('sell_order')
            ->leftJoin('customer', 'sell_order.customer_id', '=', 'customer.customer_id')
            ->select(
                'sell_order.sell_order_id',
                'sell_order.order_date',
                'sell_order.customer_id',
                'sell_order.order_completion_date',
                'sell_order.status',
                'customer.name as customer_name'
            )
            ->where('sell_order.sell_order_id', $id)
            ->first();
    
        // Fetch sell order details
        $sell_order_detail = DB::table('sell_order_detail')
            ->leftJoin('finish_product', 'finish_product.finish_product_id', '=', 'sell_order_detail.finish_product_id')
            ->select(
                'sell_order_detail.sell_order_detail_id',
                'sell_order_detail.sell_order_id',
                'sell_order_detail.order_qty_dozen',
                'sell_order_detail.finish_product_id',
                'sell_order_detail.order_quantity',
                'sell_order_detail.sale_stock', 
                'finish_product.product_name as product_name'
            )
            ->where('sell_order_detail.sell_order_id', $id)
            ->get();
    
        
        foreach ($sell_order_detail as $detail) {

            if ($detail->sale_stock == 'old') {
                $cost =  $this->oldsstockprice($detail->finish_product_id,$detail->order_quantity);
                $detail->total_cost = $cost ?? 0;
            }
            else{
                $cost =  $this->finalPriceofproduct($detail->finish_product_id,$detail->order_quantity);

                $detail->total_cost = $cost ?? 0;
            }
            
            
            
        }
     
     
        $currencies = DB::table('currency')
            ->select('currency_id', 'currency_name', 'symbol')
            ->get();

            
    
        return view('sell.create', compact('sell_order', 'sell_order_detail', 'currencies'));
}

    public function finalPriceofproduct($fid, $qty)
    {
        $total_amount = 0;
        $raw = DB::table('issue_material')
            ->where('finished_product_id', $fid)
            
            ->whereNotNull('calculation')
            ->where('calculation', '>', 0)
            ->select('issue_material.calculation as remaining_qty','issue_material.unit_cost as up')
            ->get();

        foreach ($raw as $r) {
            if ($r->remaining_qty >= $qty) {
                $q = $r->remaining_qty - $qty;
                $amount = $r->up * $qty;
                $total_amount += $amount;
                return $total_amount;
            } else {
                $q = $r->remaining_qty - $r->remaining_qty;
                $amount = $r->up * $r->remaining_qty;
                $total_amount += $amount;
                $qty -= $r->remaining_qty;

            }
        }

        return $total_amount;
    }

    public function oldsstockprice($fid, $qty)
    {
        
        $raw = DB::table('old_fproduct_stock')
            ->where('finish_product_id', $fid)
            ->select('unit_cost_price')
            ->First();

            $total_amount = $raw->unit_cost_price *  $qty;

       

        return $total_amount;
    }

     



    

   
    

    

   
    public function store(SellRequest $request)
    {
       
        $validatedData = $request->validated();
        $customer_id = $validatedData['customer_id'];
        $order_date = $validatedData['order_date'];
        $order_completion_date = $validatedData['order_completion_date'];
        $finish_product_id = $validatedData['finish_product_id'];
        $order_quantity = $validatedData['order_quantity'];
        $sale_stock = $validatedData['sale_stock'];
        

       
        // $counts = array_count_values($finish_product_id);
        // $duplicates = array_filter($counts, function($count) {
        //     return $count > 1;
        // });
        // if (!empty($duplicates)) {
        //     $msg = 'Duplicate Product found: ' . implode(', ', array_keys($duplicates));
        //     session()->flash('error', $msg);
        //     return redirect('sell'); 
        // }

        // Prepare purchase sell_orders_data and send to database
        $sell_orders_data = [
            'customer_id' => $customer_id,
            'order_date' => $order_date,
            'order_completion_date' => $order_completion_date,
            'status' => 0,
        ];
        $sell_ordersid = DB::table('sell_order')->insertGetId($sell_orders_data);

        // Prepare purchase sell_order_detail and send to database
        foreach ($finish_product_id as $i => $fpid) {

            $order_qty_dozen = $order_quantity[$i] / 12;
            $updateorder_qty_dozen = number_format($order_qty_dozen, 1);

            $sell_order_detail = [
                'sell_order_id' => $sell_ordersid,
                'finish_product_id' => $fpid,
                'order_quantity' => $order_quantity[$i],
                'order_qty_dozen' => $updateorder_qty_dozen,
                'sale_stock' => $sale_stock[$i],
                
            ];
            DB::table('sell_order_detail')->insert($sell_order_detail);


        }

        session()->flash('success', 'sell created successfully!');
        return redirect('sell');



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function createlistdetail(Request $request,string $id)
    {
        if ($request->ajax()) {
            $draw = $request->input('draw');
            
            $query = DB::table('sell_detail')
            ->leftJoin('finish_product', 'sell_detail.finish_product_id', '=', 'finish_product.finish_product_id')
            ->select(
                'sell_detail.sell_detail_id',
                'sell_detail.order_product_qty',
                'sell_detail.production_total_cost',
                'sell_detail.production_piece_cost',
                'sell_detail.unit_price',
                'sell_detail.total_price',
                'finish_product.product_name as f_name' // Ensure this alias is correct
            )->where('sell_detail.sell_id', $id);
    
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->where('finish_product.product_name', 'like', '%' . $searchValue . '%');
            }
    
            $totalRecords = $query->count();
            $query->orderBy('sell_detail.sell_detail_id', 'desc'); // Order by a valid column
        
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
        $customerName = DB::table('sell')
        ->leftJoin('customer', 'sell.customer_id', '=', 'customer.customer_id')
        ->select(
            'sell.sell_id', // Original sell_id
            DB::raw("CONCAT('INO-', sell.sell_id) as invoice_sell_id"), 
            'customer.name as customer_name'
        )->first(); 
    
        return view('createsell.detail', compact('customerName'));
    }
    public function createlist(Request $request)
    {
        if ($request->ajax()) {
            $draw = $request->input('draw');
            
            $query = DB::table('sell')
                ->leftJoin('customer', 'sell.customer_id', '=', 'customer.customer_id')
                ->leftJoin('currency', 'sell.currency_id', '=', 'currency.currency_id')
                ->select(
                    'sell.sell_id', // Original sell_id
                    DB::raw("CONCAT('INO-', sell.sell_id) as invoice_sell_id"), // Concatenated sell_id
                    'sell.total_amount',
                    'sell.builty_nbr',
                    'sell.paid_amount',
                    'sell.remaining_amount',
                    'sell.grand_production_cost',
                    'sell.transport',
                    'sell.sell_date',
                    'customer.name as customer_name',
                    'currency.symbol as currency_name'
                );
    
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->where('customer.name', 'like', '%' . $searchValue . '%');
            }
    
            $totalRecords = $query->count();
            $query->orderBy('sell.sell_id', 'desc'); // Order by a valid column
        
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
        
        return view('createsell.list');
    }
    
    public function sellorderdetail(Request $request, string $id)
    {
        if($request->ajax()) {
            $draw = $request->input('draw');
            
            $query = DB::table('sell_order_detail')
                ->leftJoin('finish_product', 'sell_order_detail.finish_product_id', '=', 'finish_product.finish_product_id')
                ->select(
                    'sell_order_detail.order_quantity',
                    'sell_order_detail.sell_order_detail_id',
                    'finish_product.product_name as product_name'
                )
                ->where('sell_order_detail.sell_order_id', $id);
    
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->where('finish_product.product_name', 'like', '%' . $searchValue . '%');
            }
    
            $totalRecords = $query->count();
            $query->orderBy('sell_order_detail.sell_order_detail_id', 'desc'); // Order by a valid column
        
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
        return view('sell.orderdetail');
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
    public function listing(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw');
    
        // Initialize the query builder
        $query = DB::table('sell_order')
            ->leftJoin('customer', 'sell_order.customer_id', '=', 'customer.customer_id')
            ->leftJoin('sell_order_detail', 'sell_order.sell_order_id', '=', 'sell_order_detail.sell_order_id')
            ->select(
                'sell_order.order_date',
                'sell_order.sell_order_id as sell_id',
                'sell_order.order_completion_date',
                'sell_order.sell_order_id',
                'sell_order.status',
                'customer.name as customer_name'
            )
            ->distinct();
    
        // Apply sorting
        // $sortColumnIndex = $request->input('order.0.column');
        // $sortDirection = $request->input('order.0.dir');
        // $sortColumnName = $request->input("columns.$sortColumnIndex.data");
        $query->orderBy('sell_order_id', 'desc');
    
        // Apply searching
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('customer.name', 'like', "%$searchValue%");
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
            if ($result->status == 2) {
                $status = 'Completed';
            } else {
                // Fetch all sell_order_details for the current sell_order
                $orderDetails = DB::table('sell_order_detail')
                    ->where('sell_order_id', $result->sell_order_id)
                    ->get();
    
                $allReady = true;
                $anyProcessing = false;
    
                foreach ($orderDetails as $orderDetail) {
                    $orderQuantity = $orderDetail->order_quantity;
                    $ordertype = $orderDetail->sale_stock;
                    $finishProductId = $orderDetail->finish_product_id;
    
                    if ($ordertype == 'old') {
                    $finishProductStock = DB::table('old_fproduct_stock')
                        ->where('finish_product_id', $finishProductId)
                        ->value('quantity');
                    } 
                    else{
                        $finishProductStock = DB::table('finish_product_stock')
                        ->where('finish_product_id', $finishProductId)
                        ->value('quantity');
                    } 
                   
    
                    if ($orderQuantity == $finishProductStock) {
                        $allReady = true;
                    }
    
                    if ($finishProductStock < $orderQuantity) {
                        $anyProcessing = true;
                    }
                }
    
                if ($anyProcessing) {
                    $status = 'Processing';
                } else {
                    $status = 'Ready';
                }
            }
    
            $modifiedResult = (object)[
                'sell_order_id' => 'SO - ' . $result->sell_order_id,
                'order_date' => $result->order_date,
                'order_completion_date' => $result->order_completion_date,
                'status' => $status,
                'customer_name' => $result->customer_name,
                'sell_id' => $result->sell_id, // Ensure this is set correctly
            ];
    
            // Add modified result to the array
            $modifiedResults[] = $modifiedResult;
        }
    
        // Prepare the response data
        $data = [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $modifiedResults,
        ];
    
        // Return the response as JSON
        return response()->json($data);
    }
    
    

    
}
