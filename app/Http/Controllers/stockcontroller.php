<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class stockcontroller extends Controller
{
    public function oldstock(){
        
        return view('stock.oldstock');
    }
    public function show(){
        
        return view('stock.list');
    }
    public function sdetail(Request $request){
        
         $draw = $request->input('draw');
        
         // Initialize the query builder
         $query = DB::table('finish_product_stock')
             ->leftJoin('finish_product', 'finish_product_stock.finish_product_id', '=', 'finish_product.finish_product_id')->select(
                 'finish_product_stock.quantity',
                 'finish_product.product_name',
                 'finish_product_stock.finish_product_stock_id',
                );

            $query->orderBy('finish_product_stock_id','desc');
         // Apply searching
         $searchValue = $request->input('search.value');
         if ($searchValue) {
             $query->where(function ($query) use ($searchValue) {
                 $query->where('finish_product.product_name', 'like', "%$searchValue%");
             });
         }
     
         // Get total records before applying pagination
         $totalRecords = $query->count();
     
         // Apply pagination
         $start = $request->input('start', 0);
         $length = $request->input('length', 10);
         $query->offset($start)->limit($length);
     
         // Apply sorting
       
         
            
         
     
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
    public function oldsdetail(Request $request){
        
         $draw = $request->input('draw');
        
         // Initialize the query builder
         $query = DB::table('old_fproduct_stock')
             ->leftJoin('finish_product', 'old_fproduct_stock.finish_product_id', '=', 'finish_product.finish_product_id')
             ->select(
                 'old_fproduct_stock.unit_cost_price',
                 'old_fproduct_stock.quantity',
                 'finish_product.product_name',
                 
                );

            $query->orderBy('old_fproduct_stock_id','desc');
         // Apply searching
         $searchValue = $request->input('search.value');
         if ($searchValue) {
             $query->where(function ($query) use ($searchValue) {
                 $query->where('finish_product.product_name', 'like', "%$searchValue%");
             });
         }
     
         // Get total records before applying pagination
         $totalRecords = $query->count();
     
         // Apply pagination
         $start = $request->input('start', 0);
         $length = $request->input('length', 10);
         $query->offset($start)->limit($length);
     
         // Apply sorting
       
         
            
         
     
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
