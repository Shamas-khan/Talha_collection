<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\karahi;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Bank;
use App\Models\Party;
use App\Models\Employee;
class roznamchaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('accounts.roznamcha.generate');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function generate(Request $request)
    {
       
        // Validate the request input
        $validatedData = $request->validate([
            'date' => 'required|date',
        ]);
    
        $date = $validatedData['date'];
       
        // Format the date to match the `created_at` column format
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
    
        // Fetch payment vouchers for the specific date
        $paymentvoucher = DB::table('paymentvoucher')
            ->leftJoin('bank', 'paymentvoucher.bank_id', '=', 'bank.bank_id')
            ->select(
                'paymentvoucher.paymentvoucher_id',
                'paymentvoucher.person_type',
                'paymentvoucher.person_id',
                'paymentvoucher.amount',
                'paymentvoucher.narration',
                'paymentvoucher.created_at',
                'bank.bank_name as account_name'
            )
            ->orderBy('paymentvoucher.paymentvoucher_id', 'desc')
            ->whereDate('paymentvoucher.created_at', $formattedDate)
            ->get();
    
        // Calculate the total amount for payment vouchers
        $paymentvouchertotalAmount = DB::table('paymentvoucher')
            ->whereDate('paymentvoucher.created_at', $formattedDate)
            ->sum('paymentvoucher.amount');
    
        // Fetch person details for payment vouchers
        foreach ($paymentvoucher as $result) {
            $personDetails = $this->fetchPersonDetails($result->person_type, $result->person_id);
            $result->person_name = $personDetails ? $personDetails->name : 'Not found';
        }
    
        // Fetch receipt vouchers for the specific date
        $reciptvoucher = DB::table('reciptvoucher')
            ->leftJoin('bank', 'reciptvoucher.bank_id', '=', 'bank.bank_id')
            ->select(
                'reciptvoucher.reciptvoucher_id',
                'reciptvoucher.person_type',
                'reciptvoucher.person_id',
                'reciptvoucher.amount',
                'reciptvoucher.narration',
                'reciptvoucher.created_at',
                'bank.bank_name as account_name'
            )
            ->orderBy('reciptvoucher.reciptvoucher_id', 'desc')
            ->whereDate('reciptvoucher.created_at', $formattedDate)
            ->get();
    
        // Calculate the total amount for receipt vouchers
        $reciptvouchertotalAmount = DB::table('reciptvoucher')
            ->whereDate('reciptvoucher.created_at', $formattedDate)
            ->sum('reciptvoucher.amount');
    
        // Fetch person details for receipt vouchers
        foreach ($reciptvoucher as $result) {
            $personDetails = $this->fetchPersonDetails($result->person_type, $result->person_id);
            $result->person_name = $personDetails ? $personDetails->name : 'Not found';
        }
    
        // Return the view with the results
        return view('accounts.roznamcha.result', compact('date', 'paymentvoucher', 'reciptvoucher', 'paymentvouchertotalAmount', 'reciptvouchertotalAmount'));
    }
    

    protected function fetchPersonDetails($personType, $personId)
    {
        switch ($personType) {
            case 'bank':
                return Bank::where('bank_id', $personId)->first(['bank_name as name']);
                break;
            case 'supplier':
                return Supplier::where('supplier_id', $personId)->first(['name']);
            case 'customer':
                return Customer::where('customer_id', $personId)->first(['name']);
            case 'vendor':
                return Vendor::where('vendor_id', $personId)->first(['name']);
            case 'karahi vendor':
                return Karahi::where('karai_vendor_id', $personId)->first(['name']);
            case 'expense':
                return ExpenseCategory::where('expense_category_id', $personId)->first(['name']);
            case 'Direct Party':
                return Party::where('parties_id', $personId)->first(['name']);
            default:
                return null;
        }
    }
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
