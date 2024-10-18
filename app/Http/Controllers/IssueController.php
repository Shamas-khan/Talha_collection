<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\FinishProduct;
use App\Models\issueMaterial;
use App\Models\FinishProductStock;
use App\Http\Requests\IssueRequest;
use App\Models\RawMaterial;
use DB;
use Carbon\Carbon;
use App\Http\Requests\FinishProductRequest;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('issue.list');
    }
    public function print(string $id)
    {
        $query = DB::table('issue_material_list')
            ->leftJoin('issue_material', 'issue_material_list.issue_material_id', '=', 'issue_material.issue_material_id')
            ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
            ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
            ->leftJoin('raw_material', 'issue_material_list.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('unit', 'raw_material.unit_id', '=', 'unit.unit_id')
            ->select(
                'issue_material_list.raw_material_id',
                'issue_material_list.issue_qty',
                'issue_material.total_quantity as product_qty',
                'vendor.name as vendor_name',
                'finish_product.product_name',
                'raw_material.name as material_name',
                'issue_material.created_at as created_at',
                'issue_material.issue_material_id as issue_material_id',
                'unit.name as unit_name',
            )
            ->where('issue_material_list.issue_material_id', $id)
            ->get();
    
        // Convert quantities to inches
        foreach ($query as $item) {

            $querys = DB::table('product_materials')
            ->where('finish_product_id', $item->finish_product_id)
            ->where('raw_material_id', $item->raw_material_id)
           ->value('material_qty');
            $item->issue_qty_in_inches = $this->convertToInches($item->issue_qty, $item->unit_name,$querys,$item->product_qty);
        }
    
        return view('print.issue', compact('query'));
    }
    
    

    
    
    public function printsingle(string $id, string $timestamp)
    {
        $query = DB::table('issue_material_list')
            ->leftJoin('issue_material', 'issue_material_list.issue_material_id', '=', 'issue_material.issue_material_id')
            ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
            ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
            ->leftJoin('raw_material', 'issue_material_list.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('unit', 'raw_material.unit_id', '=', 'unit.unit_id')
            
            ->select(
                'issue_material_list.raw_material_id',
                'issue_material_list.issue_qty',
                'issue_material.total_quantity as product_qty',
                'vendor.name as vendor_name',
                'finish_product.product_name',
                'finish_product.finish_product_id',
                'raw_material.name as material_name',
                'issue_material.created_at as created_at',
                'issue_material.issue_material_id as issue_material_id',
                'unit.name as unit_name',
            )
            ->where('issue_material_list.issue_material_id', $id)
            ->where('issue_material_list.transaction_timestamp', $timestamp) // Filter by timestamp
            ->get();

            foreach ($query as $item) {

                $querys = DB::table('product_materials')
                ->where('finish_product_id', $item->finish_product_id)
                ->where('raw_material_id', $item->raw_material_id)
               ->value('material_qty');
                $item->issue_qty_in_inches = $this->convertToInches($item->issue_qty, $item->unit_name,$querys,$item->product_qty);
            }

        return view('print.issueindividual', compact('query'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendor = Vendor::all();
        $customer = Customer::all();
        $product = FinishProduct::all();


        return view('issue.add', compact('vendor', 'product', 'customer'));
    }

    public function getfinishproductqty(Request $request)
{
    // Get the input values from the request
    $pro = $request->post("product");
    $orderqty = $request->post("orderqty");
    $unit_price = $request->post("unit_price");

    // Ensure that the values are numeric and default them if not
    $orderqty = is_numeric($orderqty) ? floatval($orderqty) : 1; // Default to 1 to avoid division by zero
    $unit_price = is_numeric($unit_price) ? floatval($unit_price) : 0;
    
    // Calculate order or unit price
    $orderOrunit = $orderqty * $unit_price;

    // Fetch raw materials and their details
    $raw = DB::table('product_materials')
        ->join('raw_material', 'product_materials.raw_material_id', '=', 'raw_material.raw_material_id')
        ->where('finish_product_id', $pro)
        ->select('product_materials.*', 'raw_material.name as raw', 'raw_material.raw_material_id as rid')
        ->get();

    $html = "";
    $total_cost_price = 0;

    foreach ($raw as $r) {
        // Ensure material_qty is numeric
        $material_qty = is_numeric($r->material_qty) ? floatval($r->material_qty) : 0;
        $requr = $material_qty * $orderqty;
        $ave = $this->getAvailableQty($r->rid);

        $html .= "<tr>";
        $html .= "<td>" . htmlspecialchars($r->raw) . "</td>";
        $html .= "<input name='raw_material_id[]' type='hidden' value='" . htmlspecialchars($r->rid) . "'>";
        $html .= "<td> <input name='require_qty[]' type='hidden' value='" . htmlspecialchars($requr) . "'>" . htmlspecialchars($requr) . "</td>";
        $html .= "<td>" . htmlspecialchars($ave) . "</td>";

        // Calculate final quantity and cost based on availability
        if ($ave >= $requr) {
            $fqty = $this->finalPrice($r->rid, $requr);
            if ($this->getDesign($r->rid, $requr) > 0) {
                $fqty = $this->getDesign($r->rid, $requr);
            }
            if ($this->getReprocessProductPrice($r->rid, $requr) > 0) {
                $fqty = $this->getReprocessProductPrice($r->rid, $requr);
            }
            $html .= "<td><input name='issue_qty[]' type='hidden' value='" . htmlspecialchars($requr) . "'><input name='remainingqty[]' type='hidden' value='" . htmlspecialchars($requr - $requr) . "'>" . htmlspecialchars($requr) . "</td>";
            $html .= "<td><input name='costprice[]' type='hidden' value='" . htmlspecialchars($fqty) . "'>" . htmlspecialchars($fqty) . "</td>";
        } else {
            $fqty = $this->finalPrice($r->rid, $ave);
            if ($this->getDesign($r->rid, $ave) > 0) {
                $fqty = $this->getDesign($r->rid, $ave);
            }
            if ($this->getReprocessProductPrice($r->rid, $ave) > 0) {
                $fqty = $this->getReprocessProductPrice($r->rid, $ave);
            }
            $html .= "<td class='text-danger'><input name='issue_qty[]' type='hidden' value='" . htmlspecialchars($ave) . "'><input name='remainingqty[]' type='hidden' value='" . htmlspecialchars($requr - $ave) . "'>" . htmlspecialchars($ave) . "</td>";
            $html .= "<td><input name='costprice[]' type='hidden' value='" . htmlspecialchars($fqty) . "'>" . htmlspecialchars($fqty) . "</td>";
        }

        $total_cost_price += $fqty;
        $html .= "</tr>";
    }

    // Calculate total cost and unit cost
    $totalCostPlusUnitprice = $total_cost_price + $orderOrunit;
    $totalprice = number_format($totalCostPlusUnitprice, 2);
    $unitcost = $orderqty != 0 ? number_format($totalCostPlusUnitprice / $orderqty, 2) : 0;

    $html .= "<tr>";
    $html .= "<td colspan='1'><strong>Total Cost Price <small>(As per Issue)</small></strong></td>";
    $html .= "<td><input name='total_cost' type='hidden' value='" . htmlspecialchars($totalprice) . "'>" . htmlspecialchars($totalprice) . "</td>";
    $html .= "<td colspan='1'><strong>Per Piece Cost <small>(As per Issue)</small></strong></td>";
    $html .= "<td><input name='unit_cost' type='hidden' value='" . htmlspecialchars($unitcost) . "'>" . htmlspecialchars($unitcost) . "</td>";
    $html .= "</tr>";

    return $html;
}


    public function getReprocessProductPrice($id, $qty)
    {
        $raw = DB::table('raw_stock')
            ->where('raw_material_id', $id)
            ->select('unit_price')
            ->first();
        if ($raw) {
            return $raw->unit_price * $qty;
        }
        return 0;
    }

    public function finalPrice($rid, $qty)
    {

        if ($rid === 173 || $rid === 174) {
            $total_amount = 0;
            $raw = DB::table('purchase_material_detail')
                ->where('raw_material_id',111)
                ->whereNotNull('remaining_qty')
                ->where('remaining_qty', '>', 0)
                ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
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
        else if ($rid === 175 || $rid === 176){
            $total_amount = 0;
            $raw = DB::table('purchase_material_detail')
                ->where('raw_material_id',110)
                ->whereNotNull('remaining_qty')
                ->where('remaining_qty', '>', 0)
                ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
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
        
        else{
            $total_amount = 0;
            $raw = DB::table('purchase_material_detail')
                ->where('raw_material_id', $rid)
                ->whereNotNull('remaining_qty')
                ->where('remaining_qty', '>', 0)
                ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
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
        
    }


    

    public function getAvailableQty($id)
    {
        if ($id === 173 || $id === 174) {

            $raw = DB::table('raw_stock')
            ->where('raw_material_id',111)
            ->select('available_quantity')
            ->first();
            if ($raw) {return $raw->available_quantity;}
            return 0;
        }
        else if ($id === 175 || $id === 176){
            $raw = DB::table('raw_stock')
            ->where('raw_material_id',110)
            ->select('available_quantity')
            ->first();
            if ($raw) {return $raw->available_quantity;}
            return 0;
        }
        else{
            $raw = DB::table('raw_stock')
                        ->where('raw_material_id', $id)
                        ->select('available_quantity')
                        ->first();
            if ($raw) {return $raw->available_quantity;}
            return 0;
        }

       
    }

    /**
     * Store a newly created resource in storage.
     */

    //  public function store(IssueRequest $request)
    //  {
        
    //      DB::beginTransaction();
       
    //      try {
    //          $vendor = $request->post('vendor');
    //          $product = $request->post('product');
    //          $qty = $request->post('total_qty');
    //          $unit_price = $request->post('unit_price');
    //          $total = $request->post('total');
    //          $customer_id = $request->post('customer_id');
    //          $remainingqty = $request->post('remainingqty');
    //          $require_qty = $request->post('require_qty');
             
    //          $total_cost = str_replace(',', '', $request->post('total_cost'));
           
    //          $unit_cost = str_replace(',', '', $request->post('unit_cost'));
           
    //          $ism = [
    //              'vendor_id' => $vendor,
    //              'finished_product_id' => $product,
    //              'total_quantity' => $qty,
    //              'received_quantity' => 0,
    //              'remaining_quantity' => $qty,
    //              'total_amount' => $total,
    //              'customer_id' => $customer_id,
    //              'calculation' => 0,
    //              'total_cost' => $total_cost,
    //              'unit_cost' => $unit_cost,
    //          ];
    //          $lastID = DB::table('issue_material')->insertGetId($ism);
     
    //          $issue_qty = $request->post('issue_qty');
     
    //          $currentValues = DB::table('vendor')
    //              ->select('paid_amount', 'total_amount', 'remaining_amount')
    //              ->where('vendor_id', $vendor)
    //              ->first();
     
    //          if ($currentValues) {
    //              $newRemainingAmount = $currentValues->remaining_amount + $total;
    //              $total_amount = $currentValues->total_amount + $total;
     
    //              DB::table('vendor')
    //                  ->where('vendor_id', $vendor)
    //                  ->update([
    //                      'remaining_amount' => $newRemainingAmount,
    //                      'total_amount' => $total_amount,
    //                  ]);

    //                  DB::table('vendor_ledger')->insert([
    //                     'vendor_id' => $vendor,
    //                     'status' => 'Production',
    //                     'narration' => 'Labour charges',
    //                     'credit' => $total,
    //                     'running_balance' => $newRemainingAmount, 
    //                     'issue_material_id' => $lastID, 
    //                 ]);
    //          }
     
    //          foreach ($request->post('raw_material_id') as $i => $raw) {
    //              $arr = [
    //                  'issue_material_id' => $lastID,
    //                  'raw_material_id' => $raw,
    //                  'issue_qty' => $issue_qty[$i],
    //                  'Required_qty' => $require_qty[$i],
    //                  'Remaining_qty' => $remainingqty[$i]
    //              ];
    //              $this->reduceQty($raw, $issue_qty[$i]);
    //              if ($this->getDesign($raw, $issue_qty[$i]) > 0) {
    //                  $fqty = $this->getDesignupdate($raw, $issue_qty[$i]);
    //              }
     
    //              $this->updateqtyForRaw($raw, $issue_qty[$i]);
     
    //              DB::table('issue_material_list')->insertGetId($arr);
    //          }
     
    //          // Commit the transaction
    //          DB::commit();
     
             
    //         return redirect()->route('pissueprintsudden', ['id' => $lastID])->with('success', 'Issue Material successfully added.');
        
    //      } catch (\Exception $e) {
    //          // Rollback the transaction
    //          DB::rollBack();
     
    //          // Store the error in the session
    //          session()->flash('error', $e->getMessage());
     
    //          return redirect()->back()->withErrors(['error' => 'There was an error processing your request.']);
    //      }
    //  }
     
    public function store(IssueRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $vendors = $request->post('vendor'); // Array of vendor IDs
            $product = $request->post('product');
            $qty = $request->post('total_qty');
            $unit_price = $request->post('unit_price');
            $total = $request->post('total');
            $customer_id = $request->post('customer_id');
            $remainingqty = $request->post('remainingqty');
            $require_qty = $request->post('require_qty');
            
            $total_cost = str_replace(',', '', $request->post('total_cost'));
            $unit_cost = str_replace(',', '', $request->post('unit_cost'));
            $issue_qty = $request->post('issue_qty'); // Array of quantities for raw materials
    
            $totalVendors = count($vendors);
    
            $lastIDs = []; // Array to store last IDs for each vendor
    
            foreach ($vendors as $index => $vendor) {
                // Create a new issue_material entry
                $ism = [
                    'vendor_id' => $vendor,
                    'finished_product_id' => $product,
                    'total_quantity' => $qty,
                    'received_quantity' => 0,
                    'remaining_quantity' => $qty,
                    'total_amount' => $total,
                    'customer_id' => $customer_id,
                    'calculation' => 0,
                    'total_cost' => $total_cost,
                    'unit_cost' => $unit_cost,
                    'labour_charges' => $unit_price,
                    'created_at' => Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s'),
                ];
    
                $lastID = DB::table('issue_material')->insertGetId($ism);
                $lastIDs[] = $lastID; // Store each last ID
    
               
    
                // Distribute raw material quantities to the current vendor
                foreach ($request->post('raw_material_id') as $i => $raw) {
                    $issueQtyPerVendor = isset($issue_qty[$i]) ? intdiv($issue_qty[$i], $totalVendors) : 0;
                    $remainingQty = isset($issue_qty[$i]) ? $issue_qty[$i] % $totalVendors : 0;
    
                    $currentIssueQty = $issueQtyPerVendor + ($index < $remainingQty ? 1 : 0);
                   
    
                    $arr = [
                        'issue_material_id' => $lastID,
                        'raw_material_id' => $raw,
                        'issue_qty' => $currentIssueQty, // Use the quantity per vendor
                        'Required_qty' => $require_qty[$i],
                        'Remaining_qty' => $require_qty[$i] - $currentIssueQty,
                        'created_at' => Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s'),
                    ];
    
                    try {
                        $this->reduceQtys($raw, $currentIssueQty);
                        if ($this->getDesign($raw, $currentIssueQty) > 0) {
                            $fqty = $this->getDesignupdate($raw, $currentIssueQty);
                        }
    
                        $this->updateqtyForRaw($raw, $currentIssueQty);
    
                        DB::table('issue_material_list')->insertGetId($arr);
                    } catch (\Exception $e) {
                        throw new \Exception("Error : " . $e->getMessage());
                    }
                }
            }
    
            // Commit the transaction
            DB::commit();
    
            return redirect()->route('pissueprintsudden', ['ids' => implode(',', $lastIDs)]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            session()->flash('error', $e->getMessage());
    
            return redirect()->back()->withErrors(['error' => 'There was an error processing your request: ' . $e->getMessage()]);
        }
    }
    
    
    

public function reduceQtys($id, $qty)
{
    if  ($id === 173 || $id === 174) {
        $raw = DB::table('raw_stock')
        ->where('raw_material_id', 111)
        ->select('raw_stock.available_quantity')
        ->first();

        if ($raw) {
        
            if ($raw->available_quantity <= 0) {
            return true; 
            }
        $fqty = $raw->available_quantity - $qty;
        if ($fqty < 0) {
            throw new \Exception('Insufficient quantity to fulfill the request.');
        }

        
        $updated = DB::table('raw_stock')
            ->where('raw_material_id', $id)
            ->update([
                'available_quantity' => $fqty
            ]);

        return $updated ? true : false;
        } else {
            return false;
        }
    }

    else if ($id === 175 || $id === 176){
        $raw = DB::table('raw_stock')
                ->where('raw_material_id', 110)
                ->select('raw_stock.available_quantity')
                ->first();
            
            if ($raw) {
            
                if ($raw->available_quantity <= 0) {
                return true; 
                }
            $fqty = $raw->available_quantity - $qty;
        if ($fqty < 0) {
            throw new \Exception('Insufficient quantity to fulfill the request.');
        }

        
        $updated = DB::table('raw_stock')
            ->where('raw_material_id', $id)
            ->update([
                'available_quantity' => $fqty
            ]);

        return $updated ? true : false;
            } else {
                return false;
            }
    }

    else {
        $raw = DB::table('raw_stock')
            ->where('raw_material_id', $id)
            ->select('raw_stock.available_quantity')
            ->first();
          
        if ($raw) {
            // Agar pehli entry me available quantity sufficient hai to sirf usi jagah se minus karein
            if ($raw->available_quantity <= 0) {
                return true; 
            }
    
            // Quantity ko calculate karein
            $fqty = $raw->available_quantity - $qty;
    
            // Agar available quantity kam hai to exception throw karein
            if ($fqty < 0) {
                throw new \Exception('Insufficient quantity to fulfill the request.');
            }
    
            // Sirf pehli matching entry ko update karein
            $updated = DB::table('raw_stock')
                ->where('raw_material_id', $id)
                ->update([
                    'available_quantity' => $fqty
                ]);
    
            // Agar update ho gaya to true return karein warna false
            return $updated ? true : false;
        } else {
            return false;
        }
    }
    

    
}

public function updateqtyForRaw($rid, $qty)
{
   
    if  ($rid === 173 || $rid === 174) {

        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', 111)
            ->whereNotNull('remaining_qty')
            ->where('remaining_qty', '>', 0)
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
            ->get();

        // Loop over raw, lekin sirf pehli entry pe minus karna hai
        foreach ($raw as $r) {
            if ($r->remaining_qty >= $qty) {
                $q = $r->remaining_qty - $qty;
                $this->updateRemaining($q, $r->pid);
                break; // Pehli dafa sufficient quantity mil gai, ab loop ko stop karo
            } else {
                $q = $r->remaining_qty - $r->remaining_qty; // Ya remaining quantity jitni bhi hai
                $qty -= $r->remaining_qty; // Adjust remaining quantity
                $this->updateRemaining(0, $r->pid);
            }
        }

    } else if ($rid === 175 || $rid === 176) {

        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', 110)
            ->whereNotNull('remaining_qty')
            ->where('remaining_qty', '>', 0)
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
            ->get();

        foreach ($raw as $r) {
            if ($r->remaining_qty >= $qty) {
                $q = $r->remaining_qty - $qty;
                $this->updateRemaining($q, $r->pid);
                break; // Pehli dafa sufficient quantity mil gai, ab loop ko stop karo
            } else {
                $q = $r->remaining_qty - $r->remaining_qty;
                $qty -= $r->remaining_qty;
                $this->updateRemaining(0, $r->pid);
            }
        }

    } else {

        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', $rid)
            ->whereNotNull('remaining_qty')
            ->where('remaining_qty', '>', 0)
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid', 'purchase_material_detail.convert_price as up')
            ->get();

        foreach ($raw as $r) {
            if ($r->remaining_qty >= $qty) {
                $q = $r->remaining_qty - $qty;
                $this->updateRemaining($q, $r->pid);
                break; // Pehli dafa sufficient quantity mil gai, ab loop ko stop karo
            } else {
                $q = $r->remaining_qty - $r->remaining_qty;
                $qty -= $r->remaining_qty;
                $this->updateRemaining(0, $r->pid);
            }
        }
    }
}

public function updateRemaining($q, $pid)
{
    DB::table('purchase_material_detail')
        ->where('purchase_material_detail_id', $pid)
        ->update([
            'remaining_qty' => $q
        ]);
}
function getDesign($rid, $qty)
{

        $total_amount = 0;
        $raw = DB::table('recieve_karahi_material_detail')
            ->where('raw_material_id', $rid)
            ->whereNotNull('remaining_qty')
            ->where('remaining_qty', '>', 0)
            ->select('recieve_karahi_material_detail.remaining_qty as remaining_qty', 'recieve_karahi_material_detail.used_material_cost as used_material_cost', 'recieve_karahi_material_detail.recieve_karahi_material_detail_id as pid', 
            'recieve_karahi_material_detail.quantity',
            'recieve_karahi_material_detail.unit_price as up')
            ->get();

        foreach ($raw as $r) {

            if ($r->remaining_qty >= $qty) {
                $materialcost = $r->used_material_cost / $r->quantity;
                $finalcost = $materialcost + $r->up;
                $q = $r->remaining_qty - $qty;
                $amount = $finalcost * $qty;
                $total_amount += $amount;

                return $total_amount;
            } else {
                $materialcost = $r->used_material_cost / $r->quantity;
                $finalcost = $materialcost + $r->up;
                $q = $r->remaining_qty - $r->remaining_qty;
                $amount = $finalcost * $r->remaining_qty;
                $total_amount += $amount;
                $qty -= $r->remaining_qty;

            }

        }
        return $total_amount;
}

public function getDesignupdate($rid, $qty)
{

    $raw = DB::table('recieve_karahi_material_detail')
        ->where('raw_material_id', $rid)
        ->whereNotNull('remaining_qty')
        ->where('remaining_qty', '>', 0)
        ->select('recieve_karahi_material_detail.remaining_qty as remaining_qty', 'recieve_karahi_material_detail.receive_karahi_material_id as pid')
        ->get();

    foreach ($raw as $r) {
        if ($r->remaining_qty >= $qty) {
            $q = $r->remaining_qty - $qty;
            $this->updatedesignremaing($q, $r->pid, $rid);

        } else {
            $q = $r->remaining_qty - $r->remaining_qty;
            $qty -= $r->remaining_qty;
            $this->updatedesignremaing($q, $r->pid, $rid);
        }
    }


}
public function updatedesignremaing($q, $pid, $rid)
{
    DB::table('recieve_karahi_material_detail')
        ->where('receive_karahi_material_id', $pid)
        ->where('raw_material_id', $rid)
        ->update([
            'remaining_qty' => $q
        ]);
}


     public function reduceQty($id, $qty)
    {
        $raw = DB::table('raw_stock')
            ->where('raw_material_id', $id)
            ->select('raw_stock.available_quantity')
            ->get()
            ->first();
            if($raw){
                    $fqty = $raw->available_quantity - $qty;
                    $updated = DB::table('raw_stock')
                        ->where('raw_material_id', $id)
                        ->update([
                            'available_quantity' => $fqty
                        ]);
                    if ($updated) {
                        return true;
                    } else {
                        return false;
                    }
                }
                else{
                    return false;
                }
    }
    
    

    public function printissuess(string $ids)
{
    
    $idsArray = explode(',', $ids);

    // Retrieve records for the given IDs
    $query = DB::table('issue_material_list')
        ->leftJoin('issue_material', 'issue_material_list.issue_material_id', '=', 'issue_material.issue_material_id')
        ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
        ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
        ->leftJoin('raw_material', 'issue_material_list.raw_material_id', '=', 'raw_material.raw_material_id')
        ->leftJoin('unit', 'raw_material.unit_id', '=', 'unit.unit_id')
        ->select(
            'issue_material_list.raw_material_id',
            'issue_material_list.issue_qty',
            'issue_material.total_quantity as product_qty',
            'vendor.name as vendor_name',
            'finish_product.finish_product_id',
            'finish_product.product_name',
            'raw_material.name as material_name',
            'issue_material.created_at as created_at',
            'issue_material.issue_material_id as issue_material_id',
            'unit.name as unit_name',
            'issue_material.vendor_id' 
        )
        ->whereIn('issue_material_list.issue_material_id', $idsArray)
        ->get();
         




    // Convert issue_qty to inches if needed
    foreach ($query as $item) {

        $querys = DB::table('product_materials')
        ->where('finish_product_id', $item->finish_product_id)
        ->where('raw_material_id', $item->raw_material_id)
       ->value('material_qty');
        
                
                
        $item->issue_qty_in_inches = $this->convertToInches($item->issue_qty, $item->unit_name,$querys,$item->product_qty);
       
    }
  

    // Group by vendor ID
    $groupedByVendor = $query->groupBy('vendor_id');

    return view('print.issue', ['vendors' => $groupedByVendor]);
}

private function convertToInches($issue_qty, $unit_name,$querys,$product_qty)
    {
        switch (strtolower($unit_name)) {
            case 'inch (2800)':
            return $product_qty / (2800 / $querys); 
             case 'inch (3200)':
            return $product_qty / (3200 / $querys);
            case 'inch (3500)':
            return $product_qty / (3500 / $querys); 
            case 'kg dori':
            return $issue_qty ;
            case 'meter(60)':
            return $issue_qty / $querys;
            case 'foot':
                return   $issue_qty / 2592; 
            case 'meter':
                return $issue_qty / 39.3701; 
            case 'gaz':
                return ($querys * $product_qty) / 36; 
            case 'gaz (60)':
                return $issue_qty / $querys; 
            case 'gaz (56)':
                return $issue_qty /$querys;
            case 'inch':
                return $issue_qty / $querys;
                case 'runner pcs':
                return $issue_qty * 0.65;
            case 'kg(12x16)':
            case 'kg(11x16)':
            case 'kg(14x18)':
            case 'kg(13x16)':
            case 'kg(11x14)':
            case 'kg(16x18)':
            case 'kg(14x22)':
                return $issue_qty / 60;
            case 'kg(30x40)':
            case 'kg(26x36)':
                return $issue_qty / 30;
            default:
                return $issue_qty; 
        }
    }


    public function printindividual(string $id)
    {
        $query = DB::table('issue_material_list')
            ->leftJoin('issue_material', 'issue_material_list.issue_material_id', '=', 'issue_material.issue_material_id')
            ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
            ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
            ->leftJoin('raw_material', 'issue_material_list.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('unit', 'raw_material.unit_id', '=', 'unit.unit_id')
            
            ->select(
                'issue_material_list.raw_material_id',
                'issue_material_list.issue_qty',
                'issue_material.total_quantity as product_qty',
                'vendor.name as vendor_name',
                'finish_product.finish_product_id',
                'finish_product.product_name',
                'raw_material.name as material_name',
                'issue_material.created_at as created_at',
                'issue_material.issue_material_id as issue_material_id',
                'unit.name as unit_name',
            )
            ->where('issue_material_list.issue_material_id', $id)
           
            ->get();

            foreach ($query as $item) {

                $querys = DB::table('product_materials')
                ->where('finish_product_id', $item->finish_product_id)
                ->where('raw_material_id', $item->raw_material_id)
               ->value('material_qty');
               
                $item->issue_qty_in_inches = $this->convertToInches($item->issue_qty, $item->unit_name,$querys,$item->product_qty);
            }

        return view('print.singleissue', compact('query'));
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
        DB::beginTransaction();
    
        try {
            $issueMaterial = DB::table('issue_material')->where('issue_material_id', $id)->first();
   
            if (!$issueMaterial) {
                return response()->json(['error' => 'Issue material not found.'], 404);
            }


           

            $readyinstock = $issueMaterial->received_quantity;
           if( $readyinstock > 0){
                        $currentQuantity = DB::table('finish_product_stock')
                        ->where('finish_product_id', $issueMaterial->finished_product_id)
                        ->value('quantity');
                    
                    $newQuantity = $currentQuantity - $readyinstock;
                    
                    DB::table('finish_product_stock')
                        ->where('finish_product_id', $issueMaterial->finished_product_id)
                        ->update([
                            'quantity' => $newQuantity
                        ]);

                        
                        DB::table('vendor_ledger')->insert([
                            'vendor_id' => $issueMaterial->vendor_id,
                            'status' => 'return',
                            'narration' => 'rec qty retrn ' ,
                            'debit' => $issueMaterial->labour_charges * $issueMaterial->received_quantity,
                            'running_balance' => 0
                            
                        ]);
           }
                


                 

                  
            // Issue material list ko pehle delete karein
            $issueMaterialList = DB::table('issue_material_list')->where('issue_material_id', $id)->get();
    
            foreach ($issueMaterialList as $item) {
                // Raw material ke quantity ko restore karain
                $this->restoreQtys($item->raw_material_id, $item->issue_qty);
                $this->updateQtyForrevert($item->raw_material_id, $item->issue_qty);
                if ($this->getDesign($item->raw_material_id, $item->issue_qty) > 0) {
                   
                    $fqty = $this->getDesignUpdaterevert($item->raw_material_id, $item->issue_qty);
                }
                
            }

            
    
            // Issue material list ko delete karein
            DB::table('issue_material_list')->where('issue_material_id', $id)->delete();
    
            // Ab issue material ko delete karein
            DB::table('issue_material')->where('issue_material_id', $id)->delete();
    
            // Transaction ko commit karein
            DB::commit();
    
            return response()->json(['success' => 'Reverted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error reverting: ' . $e->getMessage()], 500);
        }
    }
    
    







    public function restoreQtys($id, $qty)
{
    if ($id === 173 || $id === 174) {
        $raw = DB::table('raw_stock')
            ->where('raw_material_id', 111)
            ->select('raw_stock.available_quantity')
            ->first();

        if ($raw) {
            // Agar available quantity ka value negative hai to return true
            if ($raw->available_quantity < 0) {
                return true; 
            }

            // Quantity ko calculate karein
            $fqty = $raw->available_quantity + $qty;

            // Sirf pehli matching entry ko update karein
            $updated = DB::table('raw_stock')
                ->where('raw_material_id', $id)
                ->update([
                    'available_quantity' => $fqty
                ]);

            // Agar update ho gaya to true return karein warna false
            return $updated ? true : false;
        } else {
            return false;
        }
    } elseif ($id === 175 || $id === 176) {
        $raw = DB::table('raw_stock')
            ->where('raw_material_id', 110)
            ->select('raw_stock.available_quantity')
            ->first();

        if ($raw) {
            // Agar available quantity ka value negative hai to return true
            if ($raw->available_quantity < 0) {
                return true; 
            }

            // Quantity ko calculate karein
            $fqty = $raw->available_quantity + $qty;

            // Sirf pehli matching entry ko update karein
            $updated = DB::table('raw_stock')
                ->where('raw_material_id', $id)
                ->update([
                    'available_quantity' => $fqty
                ]);

            // Agar update ho gaya to true return karein warna false
            return $updated ? true : false;
        } else {
            return false;
        }
    } else {
        $raw = DB::table('raw_stock')
            ->where('raw_material_id', $id)
            ->select('raw_stock.available_quantity')
            ->first();

        if ($raw) {
            // Agar available quantity ka value negative hai to return true
            if ($raw->available_quantity < 0) {
                return true; 
            }

            // Quantity ko calculate karein
            $fqty = $raw->available_quantity + $qty;

            // Sirf pehli matching entry ko update karein
            $updated = DB::table('raw_stock')
                ->where('raw_material_id', $id)
                ->update([
                    'available_quantity' => $fqty
                ]);

            // Agar update ho gaya to true return karein warna false
            return $updated ? true : false;
        } else {
            return false;
        }
    }
}


public function updateQtyForrevert($rid, $qty)
{
    if ($rid === 173 || $rid === 174) {
        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', 111)
            ->whereNotNull('remaining_qty')
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid')
            ->get();

        // Loop over raw, lekin sirf pehli entry pe plus karna hai
        foreach ($raw as $r) {
            // Remaining quantity ko update karne ke liye
            $q = $r->remaining_qty + $qty;
            $this->updateRemaining($q, $r->pid);
            break; // Pehli dafa sufficient quantity mil gai, ab loop ko stop karo
        }
    } elseif ($rid === 175 || $rid === 176) {
        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', 110)
            ->whereNotNull('remaining_qty')
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid')
            ->get();

        foreach ($raw as $r) {
            // Remaining quantity ko update karne ke liye
            $q = $r->remaining_qty + $qty;
            $this->updateRemaining($q, $r->pid);
            break; // Pehli dafa sufficient quantity mil gai, ab loop ko stop karo
        }
    } else {
        $raw = DB::table('purchase_material_detail')
            ->where('raw_material_id', $rid)
            ->whereNotNull('remaining_qty')
            ->select('purchase_material_detail.remaining_qty as remaining_qty', 'purchase_material_detail.purchase_material_detail_id as pid')
            ->get();

        foreach ($raw as $r) {
            // Remaining quantity ko update karne ke liye
            $q = $r->remaining_qty + $qty;
            $this->updateRemaining($q, $r->pid);
            break; // Pehli dafa sufficient quantity mil gai, ab loop ko stop karo
        }
    }
}


public function getDesignUpdaterevert($rid, $qty)
{
    $raw = DB::table('recieve_karahi_material_detail')
        ->where('raw_material_id', $rid)
        ->whereNotNull('remaining_qty')
        ->where('remaining_qty', '>=', 0)
        ->select('recieve_karahi_material_detail.remaining_qty as remaining_qty', 'recieve_karahi_material_detail.receive_karahi_material_id as pid')
        ->orderBy('receive_karahi_material_id', 'desc') // Recent se shuru karein
        ->get();

    foreach ($raw as $r) {
        if ($qty > 0) {
            if ($r->remaining_qty + $qty <= $r->remaining_qty) {
                // Agar poora qty iss record pe wapas add ho sakta hai
                $newQty = $r->remaining_qty + $qty;
                $this->updateDesignRemaining($newQty, $r->pid, $rid);
                $qty = 0; // Quantity revert ho gayi
            } else {
                // Agar iss record se kuch qty add karni hai aur baqi doosre record me
                $qtyToAdd = $qty;
                $this->updateDesignRemaining($r->remaining_qty + $qtyToAdd, $r->pid, $rid);
                $qty -= $qtyToAdd; // Remaining qty ko adjust karein
            }
        }
    }
}

public function updateDesignRemaining($q, $pid, $rid)
{
    DB::table('recieve_karahi_material_detail')
        ->where('receive_karahi_material_id', $pid)
        ->where('raw_material_id', $rid)
        ->update([
            'remaining_qty' => $q
        ]);
}






public function listing(Request $request)
{
    $draw = $request->input('draw');

    // Base query
    $query = DB::table('issue_material')
        ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
        ->leftJoin('customer', 'issue_material.customer_id', '=', 'customer.customer_id')
        ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
        ->select(
            'issue_material.issue_material_id',
            'issue_material.total_quantity',
            'issue_material.received_quantity',
            'issue_material.remaining_quantity',
            DB::raw("DATE_FORMAT(issue_material.created_at, '%d-%m-%y %h:%i %p') as created_at"),
            'vendor.name as vendor_name',
            'vendor.vendor_id as vendor_id',
            'finish_product.product_name',
            'finish_product.finish_product_id as finish_product_id',
            'customer.name as customer_name',
        );

    // Apply search filter
    $searchValue = $request->input('search.value');
    if ($searchValue) {
        $query->where(function ($query) use ($searchValue) {
            $query->where('customer.name', 'like', "%$searchValue%")
                ->orWhere('vendor.name', 'like', "%$searchValue%")
                ->orWhere('issue_material.issue_material_id', 'like', "%$searchValue%")
                ->orWhere(DB::raw("DATE_FORMAT(issue_material.created_at, '%d-%m-%y %h:%i %p')"), 'like', "%$searchValue%");
        });
    }

    // Count total filtered records
    $totalFilteredRecords = $query->count();

    // Pagination
    $start = $request->input('start', 0);
    $length = $request->input('length', 10);
    $query->orderBy('issue_material.issue_material_id', 'desc')
          ->offset($start)
          ->limit($length);

    // Get filtered records
    $customers = $query->get();

    // Total records (unfiltered)
    $totalRecords = DB::table('issue_material')->count();

    // Prepare response data
    $data = [
        'draw' => (int) $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalFilteredRecords,
        'data' => $customers,
    ];

    return response()->json($data);
}

    public function getdetail(Request $request, $id)
    {
        $RawMaterial = RawMaterial::whereIn('type', [0, 1, 2])->get();
        // $RawMaterial=RawMaterial::all();
        // Fetch all data related to the specified issue_material_id
        $issueMaterials = DB::table('issue_material')
            ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
            ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
            ->select(
                'issue_material.unit_cost',
                'issue_material.issue_material_id',
                'issue_material.total_quantity',
                'issue_material.received_quantity',
                'issue_material.remaining_quantity',
                'vendor.name as vendor_name',
                'vendor.vendor_id',
                'finish_product.product_name',
                'finish_product.finish_product_id'
            )
            ->where('issue_material.issue_material_id', $id)
            ->first(); 


            $issuewalarawmaterial = DB::table('issue_material_list')
            ->leftJoin('raw_material', 'issue_material_list.raw_material_id', '=', 'raw_material.raw_material_id')
            ->select(
                'raw_material.name',
                'raw_material.raw_material_id'
            )
            ->where('issue_material_list.issue_material_id', $id)
            ->distinct() // Is line ko add karna
            ->get();
         
        
        // Check if any issue material was found
        if (!$issueMaterials) {
            // Handle the case where no data was found, e.g., redirect to a 404 page or show an error message
            abort(404, 'Issue Materials not found');
        }


        // Pass the data to the view
        return view('issue.detail', compact('issueMaterials', 'RawMaterial','issuewalarawmaterial'));
    }

    public function listdetail(Request $request, $id)
    {
        // Get the draw parameter
        $draw = $request->input('draw');

        // Initialize the query builder
        $query = DB::table('issue_material_list')
            ->leftJoin('raw_material', 'issue_material_list.raw_material_id', '=', 'raw_material.raw_material_id')
            ->leftJoin('unit', 'raw_material.unit_id', '=', 'unit.unit_id')
            ->select(
                'issue_material_list.issue_qty',
                DB::raw("DATE_FORMAT(issue_material_list.created_at,'%d-%m-%y %h:%i %p') as created_at"),
                'issue_material_list.Remaining_qty',
                'issue_material_list.Required_qty',
                'raw_material.name as raw_material_name',
                'unit.name as unit_name'
            )
            ->where('issue_material_list.issue_material_id', $id);

        $query->orderBy('issue_material_list.issue_material_list_id', 'desc');

        // Apply searching
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('raw_material.name', 'like', "%$searchValue%");
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

        // Convert issue_qty to gaz for relevant units
        foreach ($customers as $customer) {
            if ($customer->unit_name == 'gaz (60)') {
                $customer->issue_qty_in_gaz = $customer->issue_qty / (36 * 60) . " Gaz(60)";
            } elseif ($customer->unit_name == 'gaz (56)') {

                $customer->issue_qty_in_gaz = $customer->issue_qty / (36 * 56) . " Gaz(56)";
            } elseif ($customer->unit_name == 'gaz') {

                $customer->issue_qty_in_gaz = $customer->issue_qty / 36 . " Gaz";
            } elseif ($customer->unit_name == 'foot') {

                $customer->issue_qty_in_gaz = $customer->issue_qty / 12 . " Foot";
            } elseif ($customer->unit_name == 'meter') {

                $customer->issue_qty_in_gaz = $customer->issue_qty / 39.3701 . " Meter";
            } 
            elseif ($customer->unit_name == 'Kg Dori') 
            {
                 $customer->issue_qty_in_gaz = $customer->issue_qty ." inches";
            } 
            else {
                $customer->issue_qty_in_gaz = $customer->issue_qty . " " . $customer->unit_name;
            }
        }

        // Prepare the response data
        $data = [
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
            'data' => $customers, // Data to be displayed in DataTables
        ];

        // Return the response as JSON
        return response()->json($data);
    }


    public function addrawmaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'raw_material_id.*' => 'required',
            'quantity.*' => 'required|numeric|min:1',
        ]);



        if ($validator->fails()) {
            session()->flash('error', 'Kindly fill all fields correctly.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $raw_material_ids = $request->post('raw_material_id');
        $quantities = $request->post('quantity');
        $issue_material_id = $request->post('issue_material_id');
        $timestamp = now();

        DB::beginTransaction();
        try {
            foreach ($raw_material_ids as $i => $raw_material_id) {
                $query = DB::table('issue_material_list')
                    ->select('Required_qty', 'Remaining_qty')
                    ->where('raw_material_id', $raw_material_id)
                    ->where('issue_material_id', $issue_material_id)
                    ->orderByDesc('issue_material_list_id')
                    ->first();

                if ($query) {
                    $newRemainingQty = $query->Remaining_qty - $quantities[$i];

                    // Insert new record with new remaining quantity
                    DB::table('issue_material_list')->insert([
                        'issue_material_id' => $issue_material_id,
                        'raw_material_id' => $raw_material_id,
                        'Required_qty' => $query->Required_qty,
                        'Remaining_qty' => $newRemainingQty,
                        'issue_qty' => $quantities[$i],
                        'transaction_timestamp' => $timestamp,
                    ]);

                    // Calculate amount and update quantities
                    $amount = $this->finalPrice($raw_material_id, $quantities[$i]);
                    $this->reduceQty($raw_material_id, $quantities[$i]);
                    $this->updateqtyForRaw($raw_material_id, $quantities[$i]);

                    // Fetch current cost data
                    $cost = DB::table('issue_material')
                        ->select('total_cost', 'unit_cost', 'total_quantity')
                        ->where('issue_material_id', $issue_material_id)
                        ->first();

                    // Initialize new values
                    $newTotalCost = $cost ? $cost->total_cost : 0;
                    $totalQuantity = $cost ? $cost->total_quantity : 0;

                    // Add amount to total cost
                    $newTotalCost += $amount;

                    // Update design cost if applicable
                    $designamount = $this->getDesign($raw_material_id, $quantities[$i]);
                    if ($designamount > 0) {
                        $this->getDesignupdate($raw_material_id, $quantities[$i]);
                        $newTotalCost += $designamount;
                    }

                    // Calculate new unit cost
                    $newUnitCost = $totalQuantity > 0 ? $newTotalCost / $totalQuantity : 0;

                    // Update database
                    DB::table('issue_material')
                        ->where('issue_material_id', $issue_material_id)
                        ->update([
                            'total_cost' => $newTotalCost,
                            'unit_cost' => $newUnitCost
                        ]);



                } else {
                    throw new \Exception('Material is not required');
                }
            }

            DB::commit();
            return redirect()->route('issue.single', ['id' => $issue_material_id, 'timestamp' => $timestamp])->with('success', 'Issue Material successfully added.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }




    

public function rceqty(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'quantity' => 'required|integer|min:1',
        'issue_material_id' => 'required',
        'finish_product_id' => 'required',
    ]);

    if ($validator->fails()) {
        session()->flash('error', 'Quantity is required and must be greater than 0.');
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        // Start transaction
        DB::beginTransaction();

        // Retrieve the issue material record
        $issueMaterial = IssueMaterial::findOrFail($request->issue_material_id);
        $quantity = $request->quantity;

        // Update issue material quantities
        $issueMaterial->received_quantity += $quantity;
        $issueMaterial->remaining_quantity -= $quantity;
        $issueMaterial->calculation += $quantity;
        $issueMaterial->save();

        // Check if the finish product exists in the FinishProductStock model
        $finishProductStock = FinishProductStock::where('finish_product_id', $request->finish_product_id)->first();

        if ($finishProductStock) {
            // If the finish product exists, update its quantity
            $finishProductStock->quantity += $quantity;
        } else {
            // If the finish product does not exist, create a new one
            $finishProductStock = new FinishProductStock;
            $finishProductStock->finish_product_id = $request->finish_product_id;
            $finishProductStock->quantity = $quantity;
        }

        // Save the finish product stock record
        $finishProductStock->save();

        $finishproduct = DB::table('finish_product')
            ->select('product_name')
            ->where('finish_product_id', $request->finish_product_id)
            ->first();

        $issue_material = DB::table('issue_material')
            ->select('vendor_id', 'labour_charges', 'issue_material_id')
            ->where('issue_material_id', $request->issue_material_id)
            ->first();

        // Insert vendor ledger entry
        DB::table('vendor_ledger')->insert([
            'vendor_id' => $issue_material->vendor_id,
            'status' => 'Production',
            'narration' => 'rec qty:' . $quantity . ' Product: ' . $finishproduct->product_name,
            'credit' => $issue_material->labour_charges * $quantity,
            'running_balance' => 0,
            'issue_material_id' => $issue_material->issue_material_id,
        ]);

        // Update vendor amounts
        $currentValues = DB::table('vendor')
            ->select('paid_amount', 'total_amount', 'remaining_amount')
            ->where('vendor_id', $issue_material->vendor_id)
            ->first();

        if ($currentValues) {
            $newRemainingAmount = $currentValues->remaining_amount + ($issue_material->labour_charges * $quantity);
            $total_amount = $currentValues->total_amount + ($issue_material->labour_charges * $quantity);

            DB::table('vendor')
                ->where('vendor_id', $issue_material->vendor_id)
                ->update([
                    'remaining_amount' => $newRemainingAmount,
                    'total_amount' => $total_amount,
                ]);
        }

        // Commit the transaction
        DB::commit();

        // Redirect to the print route with issue_material_id
        return redirect()->route('print.receive.product', ['issue_material_id' => $request->issue_material_id, 'quantity' => $quantity]);

    } catch (\Exception $e) {
        // Rollback the transaction if something goes wrong
        DB::rollBack();
        
        // Log the error if necessary and show error message
        session()->flash('error', 'Transaction failed: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

    
    public function printReceiveProduct($issue_material_id,$quantity)
    {
        $query = DB::table('issue_material')
            ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
            ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
            ->select(
                'issue_material.total_quantity',
                'issue_material.issue_material_id',
                'issue_material.remaining_quantity',
                'issue_material.received_quantity',
                'vendor.name as vendor_name',
                'finish_product.product_name',
                'issue_material.created_at as created_at'
            )
            ->where('issue_material.issue_material_id', $issue_material_id)
            ->first();
    
        return view('print.recieveproduct', compact('query','quantity'));
    }

    public function printReceiveProducttable($issue_material_id)
    {
        $query = DB::table('issue_material')
            ->leftJoin('vendor', 'issue_material.vendor_id', '=', 'vendor.vendor_id')
            ->leftJoin('finish_product', 'issue_material.finished_product_id', '=', 'finish_product.finish_product_id')
            ->select(
                'issue_material.total_quantity',
                'issue_material.issue_material_id',
                'issue_material.remaining_quantity',
                'issue_material.received_quantity',
                'vendor.name as vendor_name',
                'finish_product.product_name',
                'issue_material.created_at as created_at'
            )
            ->where('issue_material.issue_material_id', $issue_material_id)
            ->first();
    
        return view('print.issuerectable', compact('query'));
    }
    


}