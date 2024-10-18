<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\karahi;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Bank;
use App\Models\Party;
use DB;
class ReceiptVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('accounts.recieve.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tables = [
              'bank' => 'bank',
            'customer' =>'supplier',
            // 'vendor' =>'vendor',
            // 'supplier' => 'customer',
            // 'karahi vendor' =>'karahivendor' ,
          
            'Direct Party' => 'Direct Party',
            
        ];
    
        $bank = Bank::all();
        return view('accounts.recieve.add', compact('bank', 'tables'));
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
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'person_type' => 'required',
            'person_id' => 'required',
            'amount' => 'required|numeric|min:1',
           
            'date' => 'required',
            'bank_id' => 'required',
            'narration' => 'required|string',
        ], [
            'person_type.required' => 'The Account type field is required.',
            'person_id.required' => 'Party Name is required.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a numeric value.',
           
            'bank_id.required' => 'The bank  field is required',
            'narration.required' => 'The narration field is required.',
            'narration.string' => 'The narration must be a string.',
        ]);


        try{

            $personType = $validatedData['person_type'];
            $personId = $validatedData['person_id'];
            $paidamount = $validatedData['amount'];
            $date = $validatedData['date'];
            
            $bankId = $validatedData['bank_id']; 
            $narration = $validatedData['narration'];


          

            if($personType === 'customer'){

                $bank = DB::table('bank')
                ->select('running_balance')
                ->where('bank_id', $bankId)
                ->first();
    
                $bankruning = $bank->running_balance  + $paidamount;
    
                DB::table('bank')
                ->where('bank_id', $bankId)
                ->update([
                        'running_balance' => $bankruning,
                ]);
    
                $reciptvoucher_id = DB::table('reciptvoucher')->insertGetId([
                    'person_type' => $personType,
                    'person_id' => $personId,
                    'amount' => $paidamount,
                    'bank_id' => $bankId,
                    'narration' => $narration,
                    'created_at' => $date,
                ]);
    
                DB::table('bank_ledger')->insert([
                    'status' => 'Recieved',
                    'narration' => $narration,
                    'credit' => $paidamount,
                    'bank_id' => $bankId,
                    'running_balance' => $bankruning,
                    'reciptvoucher_id' => $reciptvoucher_id,
                    'created_at' => $date,
                    
                ]);

                        $customer = DB::table('customer')
                        ->select('paid_amount', 'remaining_amount')
                        ->where('customer_id', $personId)
                        ->first();
                        $newPaidAmount = $customer->paid_amount + $paidamount;
                        $newRemainingAmount = $customer->remaining_amount - $paidamount;

                        DB::table('customer')
                        ->where('customer_id', $personId)
                        ->update([
                            'paid_amount' => $newPaidAmount,
                            'remaining_amount' => $newRemainingAmount,
                            
                        ]);

                        

                        DB::table('customer_ledger')->insert([
                            'customer_id' => $personId,
                            'status' => 'Recieved',
                            'narration' => $narration,
                            'credit' => $paidamount,
                            'running_balance' => $newRemainingAmount, 
                            'reciptvoucher_id' => $reciptvoucher_id, 
                            'created_at' => $date,
                        ]);

            }
            elseif($personType === 'bank'){

                    //   person id jo dera jari hai 
                    // bank id jis me jari hai 
                $bank = DB::table('bank')
                ->select('running_balance')
                ->where('bank_id', $bankId)
                ->first();
    
                $bankruning = $bank->running_balance  + $paidamount;
    
                DB::table('bank')
                ->where('bank_id', $bankId)
                ->update([
                        'running_balance' => $bankruning,
                ]);
    
                $reciptvoucher_id = DB::table('reciptvoucher')->insertGetId([
                    'person_type' => 'bank',
                    'person_id' => $personId,
                    'amount' => $paidamount,
                    'bank_id' =>  $bankId,
                    'narration' => $narration,
                    'created_at' => $date,
                ]);

                $paymentVoucherId = DB::table('paymentvoucher')->insertGetId([
                    'person_type' => 'bank',
                    'person_id' => $bankId,
                    'amount' => $paidamount,
                    'bank_id' => $personId,
                    'narration' => $narration,
                    'created_at' => $date,
                ]);
                        // jo payment rec kra hai 
                DB::table('bank_ledger')->insert([
                    'status' => 'Recieved',
                    'narration' => $narration,
                    'credit' => $paidamount,
                    'bank_id' => $bankId,
                    'running_balance' => 0,
                    'reciptvoucher_id' => $reciptvoucher_id,
                    'created_at' => $date,
                    
                ]);

              


                $bank = DB::table('bank')
                ->select('running_balance')
                ->where('bank_id', $personId)
                ->first();
                
                $newRemainingAmount = $bank->running_balance - $paidamount;

                DB::table('bank')
                ->where('bank_id', $personId)
                ->update([
                        'running_balance' => $newRemainingAmount,
                ]);

                

                DB::table('bank_ledger')->insert([
                    'bank_id' => $personId,
                    'status' => 'Recieved',
                    'narration' => $narration,
                    'debit' => $paidamount,
                    'running_balance' => $newRemainingAmount, 
                    'paymentvoucher_id' => $paymentVoucherId, 
                    'created_at' => $date,
                ]);

            }
            else{


                $bank = DB::table('bank')
                ->select('running_balance')
                ->where('bank_id', $bankId)
                ->first();
    
                $bankruning = $bank->running_balance  + $paidamount;
    
                DB::table('bank')
                ->where('bank_id', $bankId)
                ->update([
                        'running_balance' => $bankruning,
                ]);
    
                $reciptvoucher_id = DB::table('reciptvoucher')->insertGetId([
                    'person_type' => $personType,
                    'person_id' => $personId,
                    'amount' => $paidamount,
                    'bank_id' => $bankId,
                    'narration' => $narration,
                    'created_at' => $date,
                ]);
    
                DB::table('bank_ledger')->insert([
                    'status' => 'Recieved',
                    'narration' => $narration,
                    'credit' => $paidamount,
                    'bank_id' => $bankId,
                    'running_balance' => $bankruning,
                    'reciptvoucher_id' => $reciptvoucher_id,
                    'created_at' => $date,
                    
                ]);

                $parties = DB::table('parties')
                ->select('paid_amount', 'remaining_amount','total_amount')
                ->where('parties_id', $personId)
                ->first();
               
                // $newtoalAmount = $parties->total_amount + $paidamount;
                $newRemainingAmount = $parties->remaining_amount + $paidamount;
    
                DB::table('parties')
                ->where('parties_id', $personId)
                ->update([
                    // 'total_amount' => $newtoalAmount,
                    'remaining_amount' => $newRemainingAmount,
                    
                ]);
                DB::table('party_ledger')->insert([
                    'party_id' => $personId,
                    'status' => 'Recieved',
                    'narration' => $narration,
                    'credit' => $paidamount,
                    'reciptvoucher_id' => $reciptvoucher_id, 
                    'created_at' => $date,
                ]);
            }






            DB::commit();
            return redirect()->back()->with('success', 'Payment Recieve successfully.');

        }
        catch (\Exception $e) {
            DB::rollBack(); 
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
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

    public function listing(Request $request)
    {
        
        
        $query = DB::table('reciptvoucher')
            ->leftJoin('bank', 'reciptvoucher.bank_id', '=', 'bank.bank_id')
            ->select(
                'reciptvoucher.reciptvoucher_id',
                'reciptvoucher.person_type',
                'reciptvoucher.person_id',
                'reciptvoucher.amount',
                'reciptvoucher.narration',
                'reciptvoucher.created_at',
                'bank.bank_name as bank_name' 
            )->orderBy('reciptvoucher.reciptvoucher_id', 'desc');
       
    
       
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('reciptvoucher.narration', 'like', "%$searchValue%")
                  ->orWhere('bank.bank_name', 'like', "%$searchValue%");
                // Add more search conditions for other columns if needed
            });
        }
    
        
        $totalRecords = $query->count();
    
     
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);
    
        // Fetching results
        $results = $query->get();
    
        // Adding person details
        foreach ($results as $result) {
            $personDetails = $this->fetchPersonDetails($result->person_type, $result->person_id);
            $result->person_name = $personDetails ? $personDetails->name : 'Not found'; 
        }
        
        $data = [
            'draw' => $request->input('draw',1),
            'recordsTotal' => $totalRecords, 
            'recordsFiltered' => $totalRecords, 
            'data' => $results
        ];
       
        return response()->json($data);
    }
    
   
}
