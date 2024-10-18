<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class homeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function home()
    {
        try {
           $data = $this->fetchData();



        } catch (\Exception $e) {
            
           
            $data = [
                'customertotalRemainingAmount' => 0,
                'suppliertotalRemainingAmount' => 0,
                'karai_vendor' => 0,
                'vendor' => 0,
                'parties' => 0,
                'employee' => 0,
                'bank' => collect()
            ];
        }
    
        
        return view('welcome', $data);
    }
    private function fetchData()
{
   
    $getSum = function ($table) {
        return number_format(DB::table($table)->sum('remaining_amount'));
    };

    
    $data = [
        'customertotalRemainingAmount' => $getSum('customer'),
        'suppliertotalRemainingAmount' => $getSum('supplier'),
        'karai_vendor' => $getSum('karai_vendor'),
        'vendor' => $getSum('vendor'),
        'parties' => $getSum('parties'),
        'employee' => $getSum('employees'),
        'bank' => DB::table('bank')
            ->select('bank_id', 'bank_name', 'running_balance')
            ->orderBy('bank_id', 'desc')
            ->get()
    ];

    return $data;
}
    
    public function index()
    {
       
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
