<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
class reportsController extends Controller
{
    
     public function summaryreport(Request $request){

            return view('reports.summary.index');
     }


     public function summaryreportgenerate(Request $request)
     {
        $validatedData = $request->validate([
            'sdate' => 'required|date',
            'edate' => 'required|date|after_or_equal:sdate',
        ]);

        $startdate = $validatedData['sdate'];
        $enddate = $validatedData['edate'];
        $sdate = Carbon::parse($startdate)->format('Y-m-d');
        $edate = Carbon::parse($enddate)->format('Y-m-d');


        $selldata = DB::table('sell')
                        ->select(
                            DB::raw('SUM(grand_production_cost) as total_production_cost'),
                            DB::raw('SUM(total_amount) as total_amount'),
                            DB::raw('SUM(total_amount - grand_production_cost) as total_profit')
                        )
                        ->whereBetween('sell.sell_date', [$sdate, $edate])
                        ->first();

        $employeesalaary = DB::table('paymentvoucher')
                        ->select(
                            DB::raw('SUM(amount) as total_employee_salary'),      
                        )
                        ->where('person_type','Employee')
                        ->whereBetween('paymentvoucher.created_at', [$sdate, $edate])
                        ->first();

        $expenseeamount = DB::table('paymentvoucher')
                        ->select(
                            DB::raw('SUM(amount) as total_expense_salary'),      
                        )
                        ->where('person_type','expense')
                        ->whereBetween('paymentvoucher.created_at', [$sdate, $edate])
                        ->first();

        $sumofmonthcosting = $selldata->total_production_cost + $expenseeamount->total_expense_salary + $employeesalaary->total_employee_salary;

        $netprofit =  $sumofmonthcosting - $selldata->total_profit ;
        
        
    $result = [
                'sell_total' => $selldata->total_amount,
                'sell_making_total' => $selldata->total_production_cost,
                'sell_profit' => $selldata->total_profit,
                'start_date' => $sdate,
                'end_date' => $edate,
                'employeesalaary' => $employeesalaary->total_employee_salary,
                'expensee' => $expenseeamount->total_expense_salary,
                'sumofmonthcosting' => $sumofmonthcosting,
                'netprofit' => $netprofit,
               
             ];

            

     return view('reports.summary.result', compact('result'));

     }
     public function profit_and_loss(Request $request)
     {
         // Validate the request input
         $validatedData = $request->validate([
             'sdate' => 'required|date',
             'edate' => 'required|date|after_or_equal:sdate',
         ]);
         $startdate = $validatedData['sdate'];
         $enddate = $validatedData['edate'];
         $sdate = Carbon::parse($startdate)->format('Y-m-d');
         $edate = Carbon::parse($enddate)->format('Y-m-d');
     
         // Fetch data with pagination, limiting to 20 records per page
         $data = DB::table('sell')
             ->leftJoin('customer', 'sell.customer_id', '=', 'customer.customer_id')
             ->leftJoin('currency', 'sell.currency_id', '=', 'currency.currency_id')
             ->select(
                 'sell.sell_id',
                 'sell.total_amount',
                 'sell.grand_production_cost',
                 'sell.sell_date',
                 'currency.symbol as currency_symbol',
                 'customer.name as customer_name',
                 DB::raw('(sell.total_amount - sell.grand_production_cost) as profit')
             )
             ->whereBetween('sell.sell_date', [$sdate, $edate])
             ->orderBy('sell.sell_id', 'desc')
             ->paginate(100); // Paginate with 20 records per page
     
         // Calculate total production cost, total amount, and total profit
         $totals = DB::table('sell')
             ->select(
                 DB::raw('SUM(grand_production_cost) as total_production_cost'),
                 DB::raw('SUM(total_amount) as total_amount'),
                 DB::raw('SUM(total_amount - grand_production_cost) as total_profit')
             )
             ->whereBetween('sell.sell_date', [$sdate, $edate])
             ->first();
     
         if ($data->isEmpty()) {
             return redirect()->back()->with('error', 'No records found for the selected date range.');
         }
     
         // Prepare the result array
         $result = [
             'total_amount' => $totals->total_amount,
             'total_production_cost' => $totals->total_production_cost,
             'total_profit' => $totals->total_profit,
             'sell' => $data,
         ];
     
         // Return the view with the result data and date range
         return view('reports.profit_and_loss.result_profit', compact('result', 'sdate', 'edate'));
     }
     
     public function index()
     {
         return view('reports.profit_and_loss.index');
     }
     

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
