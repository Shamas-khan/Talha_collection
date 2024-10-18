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
use App\Models\Employee;
use DB;

class paymentController extends Controller
{
   
    public function index()
    {
        

       
        return view('accounts.payment.list');
    }

    
    public function create()
    {
        $tables = [
            // 'customer' =>'supplier',
            'vendor' =>'vendor',
            'supplier' => 'customer',
            'karahi vendor' =>'karahivendor' ,
            'expense' => 'expense',
            'Direct Party' => 'Direct Party',
            'Employee' => 'Employee',
            'customer' => 'customer',
            
        ];
    
        $bank = Bank::all();
        return view('accounts.payment.add', compact('bank', 'tables'));
    }
    
 
    public function getPayables($type)
{
    switch ($type) {
        case 'bank':
            $payables = Bank::all(['bank_id as id', 'Bank_name as name','running_balance as running']);
            break;
        case 'supplier':
            $payables = Supplier::all(['supplier_id as id', 'name as name','remaining_amount as running']);
            break;
        case 'customer':
            $payables = Customer::all(['customer_id as id', 'name as name' ,'remaining_amount as running']);
            break;
        case 'vendor':
            $payables = Vendor::all(['vendor_id as id', 'name as name' ,'remaining_amount as running']);
            break;
        case 'karahi vendor':
            $payables = karahi::all(['karai_vendor_id as id', 'name as name' ,'remaining_amount as running']);
            break;
        case 'expense':
            $payables = ExpenseCategory::all(['expense_category_id as id', 'name as name' ]);
            break;
        case 'Direct Party':
            $payables = Party::all(['parties_id as id', 'name as name','remaining_amount as running' ]);
            break;
        case 'Employee':
            $payables = Employee::all(['employee_id as id', 'name as name','remaining_amount as running' ]);
            break;
        default:
            return response()->json(['message' => 'Invalid type'], 400);
    }

    return response()->json($payables);
}


public function update(Request $request, string $id)
{
    $validatedData = $request->validate([
        'person_type' => 'required',
        'person_id' => 'required',
        'oldnakid' => 'required',
        'amount' => 'required|numeric|min:1',
       
        'bank_id' => 'required',
        'date' => 'required',
        'narration' => 'required|string',
    ], [
        'person_type.required' => 'The Account type field is required.',
        'person_id.required' => 'Party Name is required.',
        'oldnakid.required' => 'Party Name is required.',
        'amount.required' => 'The amount field is required.',
        'amount.numeric' => 'The amount must be a numeric value.',
       
        'bank_id.required' => 'The bank  field is required',
        'narration.required' => 'The narration field is required.',
        'narration.string' => 'The narration must be a string.',
    ]);

    // dd($request->all());
    try {

        DB::beginTransaction();
        $paymentVoucher = DB::table('paymentvoucher')->where('paymentvoucher_id', $id)->first();
        if (!$paymentVoucher) {
            DB::rollBack();
            throw new \Exception('Payment voucher not found.');
        }

        $oldAmount = $paymentVoucher->amount;
        $personType = $validatedData['person_type'];
        $oldnakid = $validatedData['oldnakid'];
        $personId = $validatedData['person_id'];
        $paidAmount = $validatedData['amount'];
        $date = $validatedData['date'];
        $bankId = $validatedData['bank_id'];
        $narration = $validatedData['narration'];

        DB::table('bank_ledger')->where('paymentvoucher_id', $id)->delete();

        $oldnak = DB::table('bank')->select('running_balance')->where('bank_id', $oldnakid)->first();
        $newRunningBalancesold = $oldnak->running_balance + $oldAmount ;
        DB::table('bank')->where('bank_id', $oldnakid)->update(['running_balance' => $newRunningBalancesold]);

        
        // Update the bank running balance
   

        $banks = DB::table('bank')->where('bank_id', $bankId)->first();
         if (!$banks || $banks->running_balance + $oldAmount <= $paidAmount) {
             DB::rollBack();
             throw new \Exception('Insufficient balance in the bank.');
         }
         $bank = DB::table('bank')->select('running_balance')->where('bank_id', $bankId)->first();
         $newRunningBalance = $bank->running_balance - $paidAmount;
         DB::table('bank')->where('bank_id', $bankId)->update(['running_balance' => $newRunningBalance]);
         // Delete old payment voucher entries in related tables
        

         // Insert into the bank ledger
         DB::table('bank_ledger')->insert([
            'status' => 'Payment',
            'narration' => $narration,
            'debit' => $paidAmount,
            'bank_id' => $bankId,
            'running_balance' => $newRunningBalance,
            'created_at' => $date,
            'paymentvoucher_id' => $id,
        ]);

            DB::table('paymentvoucher')->where('paymentvoucher_id', $id)->update([
                        'person_type' => $personType,
                        'person_id' => $personId,
                        'amount' => $paidAmount,
                        'bank_id' => $bankId,
                        'narration' => $narration,
                      
                    ]);
                    

       
         if ($personType === 'supplier') {

             DB::table('supplier_ledger')->where('paymentvoucher_id', $id)->delete();

             $supplier = DB::table('supplier')->select('paid_amount', 'remaining_amount')->where('supplier_id', $personId)->first();
             $newPaidAmount = $supplier->paid_amount + $paidAmount - $oldAmount;
             $newRemainingAmount = $supplier->remaining_amount - $paidAmount + $oldAmount;
 
             DB::table('supplier')->where('supplier_id', $personId)->update([
                 'paid_amount' => $newPaidAmount,
                 'remaining_amount' => $newRemainingAmount,
             ]);
 
             DB::table('supplier_ledger')->insert([
                 'supplier_id' => $personId,
                 'status' => 'Payment',
                 'narration' => $narration,
                 'debit' => $paidAmount,
                 'running_balance' => $newRemainingAmount,
                 'paymentvoucher_id' => $id,
                 'created_at' => $date,
             ]);

         } elseif ($personType === 'customer') {
             DB::table('customer_ledger')->where('paymentvoucher_id', $id)->delete();

             $customer = DB::table('customer')->select('paid_amount', 'remaining_amount')->where('customer_id', $personId)->first();
             $newPaidAmount = $customer->paid_amount + $paidAmount - $oldAmount;
             $newRemainingAmount = $customer->remaining_amount - $paidAmount + $oldAmount;
 
             DB::table('customer')->where('customer_id', $personId)->update([
                 'paid_amount' => $newPaidAmount,
                 'remaining_amount' => $newRemainingAmount,
             ]);
 
             DB::table('customer_ledger')->insert([
                 'customer_id' => $personId,
                 'status' => 'Payment',
                 'narration' => $narration,
                 'credit' => $paidAmount,
                 'running_balance' => $newRemainingAmount, 
                 'paymentvoucher_id' => $id, 
                 'created_at' => $date,
             ]);
         } elseif ($personType === 'karahi vendor') {
             DB::table('karahivendor_ledger')->where('paymentvoucher_id', $id)->delete();

            

            $karai_vendor = DB::table('karai_vendor')->select('paid_amount', 'remaining_amount')->where('karai_vendor', $personId)->first();
                $newPaidAmount = $karai_vendor->paid_amount + $paidAmount - $oldAmount;
             $newRemainingAmount = $karai_vendor->remaining_amount - $paidAmount + $oldAmount;
 
             DB::table('karai_vendor')->where('karai_vendor', $personId)->update([
                 'paid_amount' => $newPaidAmount,
                 'remaining_amount' => $newRemainingAmount,
             ]);
 
             DB::table('karahivendor_ledger')->insert([
                 'karai_vendor_id' => $personId,
                 'status' => 'Payment',
                 'narration' => $narration,
                 'credit' => $paidAmount,
                 'running_balance' => 0, 
                 'paymentvoucher_id' => $id, 
                 'created_at' => $date,
             ]);



         } elseif ($personType === 'vendor') {
             DB::table('vendor_ledger')->where('paymentvoucher_id', $id)->delete();


             $vendor = DB::table('vendor')->select('paid_amount', 'remaining_amount')->where('vendor_id', $personId)->first();
             $newPaidAmount = $vendor->paid_amount + $paidAmount - $oldAmount;
             $newRemainingAmount = $vendor->remaining_amount - $paidAmount + $oldAmount;
 
             DB::table('vendor')->where('vendor_id', $personId)->update([
                 'paid_amount' => $newPaidAmount,
                 'remaining_amount' => $newRemainingAmount,
             ]);
 
             DB::table('vendor_ledger')->insert([
                 'status' => 'Payment',
                 'narration' => $narration,
                 'credit' => $paidAmount,
                 'running_balance' => 0, 
                 'paymentvoucher_id' => $id, 
                 'vendor_id' => $personId,
                 'created_at' => $date,
             ]);

         } elseif ($personType === 'Employee') {
             DB::table('employee_ledger')->where('paymentvoucher_id', $id)->delete();

            $employees = DB::table('employees')->select('remaining_amount')->where('employee_id', $personId)->first();
            
             $newRemainingAmount = $employees->remaining_amount - $paidAmount + $oldAmount;
 
             DB::table('employees')->where('employee_id', $personId)->update([
              
                 'remaining_amount' => $newRemainingAmount,
             ]);
 
             DB::table('employee_ledger')->insert([
                 'employee_id' => $personId,
                 'status' => 'Payment',
                 'narration' => $narration,
                 'credit' => $paidAmount,
                 'running_balance' => 0, 
                 'paymentvoucher_id' => $id, 
                 'created_at' => $date,
             ]);

         }
         elseif($personType === 'expense'){
            DB::table('expense')->where('paymentvoucher_id', $id)->delete();

            DB::table('expense')->insert([
                'expense_category_id' => $personId,
                'reason' => $narration,
                'amount' => $paidAmount,
                'created_at' => $date,

            ]);

         }
         else{
            DB::table('party_ledger')->where('paymentvoucher_id', $id)->delete();

            $parties = DB::table('parties')->select('paid_amount', 'remaining_amount')->where('parties_id', $personId)->first();
            $newPaidAmount = $parties->paid_amount + $paidAmount - $oldAmount;
            $newRemainingAmount = $parties->remaining_amount - $paidAmount + $oldAmount;

            DB::table('parties')->where('parties_id', $personId)->update([
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
            ]);
 
             DB::table('party_ledger')->insert([
                 'party_id' => $personId,
                 'status' => 'Payment',
                 'narration' => $narration,
                 'credit' => $paidAmount,
                 
                 'paymentvoucher_id' => $id, 
                 'created_at' => $date,
             ]);
         }


         
       

        DB::commit();
        return redirect()->back()->with('success', 'Payment voucher updated successfully.');
   
    }
    catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
    

public function store(Request $request)
{

        $validatedData = $request->validate([
            'person_type' => 'required',
            'person_id' => 'required',
            'amount' => 'required|numeric|min:1',
           
            'bank_id' => 'required',
            'date' => 'required',
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

            DB::beginTransaction();
        
        $personType = $validatedData['person_type'];
        $personId = $validatedData['person_id'];
        $paidamount = $validatedData['amount'];
        $date = $validatedData['date'];
        
        $bankId = $validatedData['bank_id']; 
        $narration = $validatedData['narration'];

        $bankruning ;

        
            $bank = DB::table('bank')
                ->select('running_balance')
                ->where('bank_id', $bankId)
                ->first();

            if (!$bank || $bank->running_balance < $paidamount) {
                DB::rollBack();
                throw new \Exception('Insufficient balance in the bank.');
            }
            else{
                $bankruning = $bank->running_balance  - $paidamount;
                DB::table('bank')
                    ->where('bank_id', $bankId)
                    ->update([
                            'running_balance' => $bankruning,
                    ]);
            }
        
        $paymentVoucherId = DB::table('paymentvoucher')->insertGetId([
            'person_type' => $personType,
            'person_id' => $personId,
            'amount' => $paidamount,
            'bank_id' => $bankId,
            'narration' => $narration,
            'created_at' => $date,
        ]);

        
            DB::table('bank_ledger')->insert([
                'status' => 'Payment',
                'narration' => $narration,
                'debit' => $paidamount,
                'bank_id' => $bankId,
                'running_balance' => $bankruning,
                'created_at' => $date,
                'paymentvoucher_id' => $paymentVoucherId
                
            ]);
        
       
        if($personType === 'supplier'){

            $supplier = DB::table('supplier')
            ->select('paid_amount', 'total_amount', 'remaining_amount')
            ->where('supplier_id', $personId)
            ->first();
            
            $newPaidAmount = $supplier->paid_amount + $paidamount;
            $newRemainingAmount = $supplier->remaining_amount - $paidamount;
            // $totalAmount = $supplier->total_amount + $paidamount;

            DB::table('supplier')
            ->where('supplier_id', $personId)
            ->update([
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                // 'total_amount' => $totalAmount,
            ]);
            DB::table('supplier_account')->insert([
                'supplier_id' => $personId,
                'narration' => $narration,
                'paid_amount' => $paidamount,
                'created_at' => $date,
            ]);

           
            DB::table('supplier_ledger')->insert([
                'supplier_id' => $personId,
                'status' => 'Payment',
                'narration' => $narration,
                'debit' => $paidamount,
                'running_balance' => $newRemainingAmount, 
                'paymentvoucher_id' => $paymentVoucherId, 
                'created_at' => $date,
            ]);

            
           
        }
        elseif($personType === 'customer'){

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

            DB::table('customer_payment')->insert([
                'customer_id' => $personId,
                'narration' => $narration,
                'paid_amount' => $paidamount,
            ]);

            DB::table('customer_ledger')->insert([
                'customer_id' => $personId,
                'status' => 'Payment',
                'narration' => $narration,
                'credit' => $paidamount,
                'running_balance' => $newRemainingAmount, 
                'paymentvoucher_id' => $paymentVoucherId, 
            ]);

        }
        elseif($personType === 'karahi vendor'){

            $karai_vendor = DB::table('karai_vendor')
            ->select('paid_amount', 'remaining_amount')
            ->where('karai_vendor_id', $personId)
            ->first();
            $newPaidAmount = $karai_vendor->paid_amount + $paidamount;
            $newRemainingAmount = $karai_vendor->remaining_amount - $paidamount;

            DB::table('karai_vendor')
            ->where('karai_vendor_id', $personId)
            ->update([
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                
            ]);

            DB::table('karahi_vendor_payment')->insert([
                'karai_vendor_id' => $personId,
                'narration' => $narration,
                'paid_amount' => $paidamount,
                'created_at' => $date,
            ]);

            DB::table('karahivendor_ledger')->insert([
                'karai_vendor_id' => $personId,
                'status' => 'Payment',
                'narration' => $narration,
                'debit' => $paidamount,
                'running_balance' => $newRemainingAmount, 
                'paymentvoucher_id' => $paymentVoucherId,
                'created_at' => $date, 
            ]);

        }
        elseif($personType === 'vendor'){

            $vendor = DB::table('vendor')
            ->select('paid_amount', 'remaining_amount')
            ->where('vendor_id', $personId)
            ->first();
            $newPaidAmount = $vendor->paid_amount + $paidamount;
            $newRemainingAmount = $vendor->remaining_amount - $paidamount;

            DB::table('vendor')
            ->where('vendor_id', $personId)
            ->update([
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                
            ]);

            DB::table('vendor_payment')->insert([
                'vendor_id' => $personId,
                'narration' => $narration,
                'paid_amount' => $paidamount,
                'created_at' => $date,
            ]);

            DB::table('vendor_ledger')->insert([
                'vendor_id' => $personId,
                'status' => 'Payment',
                'narration' => $narration,
                'debit' => $paidamount,
                'running_balance' => $newRemainingAmount, 
                'paymentvoucher_id' => $paymentVoucherId, 
                'created_at' => $date,
            ]);
        }
        elseif($personType === 'expense'){
            DB::table('expense')->insert([
                'expense_category_id' => $personId,
                'reason' => $narration,
                'amount' => $paidamount,
                'created_at' => $date,

            ]);

        }
        elseif($personType === 'Employee'){
            
            $employee = DB::table('employees')
            ->select('remaining_amount')
            ->where('employee_id', $personId)
            ->first();
            
            $newRemainingAmount = $employee->remaining_amount - $paidamount;

            DB::table('employees')
            ->where('employee_id', $personId)
            ->update([
                     'remaining_amount' => $newRemainingAmount,
                
            ]);

            DB::table('employee_ledger')->insert([
                'employee_id' => $personId,
                'status' => 'Payment',
                // 'transaction_date' => now(),
                'description' => $narration,
                'debit' => $paidamount,
                'paymentvoucher_id' => $paymentVoucherId,
                'transaction_date' => $date, 
            ]);

        }
        else{
            $parties = DB::table('parties')
            ->select('paid_amount', 'remaining_amount','total_amount')
            ->where('parties_id', $personId)
            ->first();
            $newPaidAmount = $parties->paid_amount + $paidamount;
            $newtoalAmount = $parties->total_amount + $paidamount;
            $newRemainingAmount = $parties->remaining_amount - $paidamount;

            DB::table('parties')
            ->where('parties_id', $personId)
            ->update([
                'total_amount' => $newtoalAmount,
                'paid_amount' => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                
            ]);
            DB::table('party_ledger')->insert([
                'party_id' => $personId,
                'status' => 'Payment',
                'narration' => $narration,
                'debit' => $paidamount,
                'paymentvoucher_id' => $paymentVoucherId, 
                'created_at' => $date,
            ]);
        }

        
        DB::commit();
        return redirect()->back()->with('success', 'Payment recorded successfully.');

        }catch (\Exception $e) {
            DB::rollBack(); 
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }



}

   

   
    public function show(string $id)
    {
    }

   
    public function edit(string $id)
    { 
        $paymentVoucher = DB::table('paymentvoucher')
        ->where('paymentvoucher_id', $id)
        ->where('person_type', '!=', 'bank')
        ->first();
      

        if (!$paymentVoucher) {
            return redirect()->back()->with('error', 'Bank to bank payments Edit only in Recieve Voucher.');
        }

        // dd($paymentVoucher);
    
     
        $bank = DB::table('bank')->get();
        $tables = [
            // 'customer' =>'supplier',
            'vendor' =>'vendor',
            'supplier' => 'customer',
            'karahi vendor' =>'karahivendor' ,
            'expense' => 'expense',
            'Direct Party' => 'Direct Party',
            'Employee' => 'Employee',
            'customer' => 'customer',
            
        ];
       
        return view('accounts.payment.edit', compact('paymentVoucher', 'bank','tables'));
    }

   
   

   
    
    public function detete(string $id)
{
    try {
        DB::beginTransaction();

        // Payment voucher details
        $paymentVoucher = DB::table('paymentvoucher')->where('paymentvoucher_id', $id)->first();
        if (!$paymentVoucher) {
            DB::rollBack();
            throw new \Exception('Payment voucher not found.');
        }



        $oldAmount = $paymentVoucher->amount;
        $personType = $paymentVoucher->person_type; // Retrieve person type from voucher
        $personId = $paymentVoucher->person_id; // Retrieve person ID from voucher
        $oldnakid = $paymentVoucher->bank_id; // Retrieve old bank ID from voucher

        // Remove related entries from the bank ledger
        DB::table('bank_ledger')->where('paymentvoucher_id', $id)->delete();

        // Update the old bank running balance
        $oldnak = DB::table('bank')->select('running_balance')->where('bank_id', $oldnakid)->first();
        $newRunningBalanceOld = $oldnak->running_balance + $oldAmount;
        DB::table('bank')->where('bank_id', $oldnakid)->update(['running_balance' => $newRunningBalanceOld]);

        // Depending on person type, update the related ledger and remaining amounts
        switch ($personType) {
            case 'supplier':
                DB::table('supplier_ledger')->where('paymentvoucher_id', $id)->delete();
                $supplier = DB::table('supplier')->select('paid_amount', 'remaining_amount')->where('supplier_id', $personId)->first();
                $newPaidAmount = $supplier->paid_amount - $oldAmount;
                $newRemainingAmount = $supplier->remaining_amount + $oldAmount;

                DB::table('supplier')->where('supplier_id', $personId)->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                ]);
                break;

            case 'customer':
                DB::table('customer_ledger')->where('paymentvoucher_id', $id)->delete();
                $customer = DB::table('customer')->select('paid_amount', 'remaining_amount')->where('customer_id', $personId)->first();
                $newPaidAmount = $customer->paid_amount - $oldAmount;
                $newRemainingAmount = $customer->remaining_amount + $oldAmount;

                DB::table('customer')->where('customer_id', $personId)->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                ]);
                break;

            case 'karahi vendor':
                DB::table('karahivendor_ledger')->where('paymentvoucher_id', $id)->delete();
                $karaiVendor = DB::table('karai_vendor')->select('paid_amount', 'remaining_amount')->where('karai_vendor', $personId)->first();
                $newPaidAmount = $karaiVendor->paid_amount - $oldAmount;
                $newRemainingAmount = $karaiVendor->remaining_amount + $oldAmount;

                DB::table('karai_vendor')->where('karai_vendor', $personId)->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                ]);
                break;

            case 'vendor':
                DB::table('vendor_ledger')->where('paymentvoucher_id', $id)->delete();
                $vendor = DB::table('vendor')->select('paid_amount', 'remaining_amount')->where('vendor_id', $personId)->first();
                $newPaidAmount = $vendor->paid_amount - $oldAmount;
                $newRemainingAmount = $vendor->remaining_amount + $oldAmount;

                DB::table('vendor')->where('vendor_id', $personId)->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                ]);
                break;

            case 'Employee':
                DB::table('employee_ledger')->where('paymentvoucher_id', $id)->delete();
                $employees = DB::table('employees')->select('remaining_amount')->where('employee_id', $personId)->first();
                $newRemainingAmount = $employees->remaining_amount + $oldAmount;

                DB::table('employees')->where('employee_id', $personId)->update([
                    'remaining_amount' => $newRemainingAmount,
                ]);
                break;

            case 'expense':
                DB::table('expense')->where('paymentvoucher_id', $id)->delete();
                break;

            default:
                DB::table('party_ledger')->where('paymentvoucher_id', $id)->delete();
                $parties = DB::table('parties')->select('paid_amount', 'remaining_amount')->where('parties_id', $personId)->first();
                $newPaidAmount = $parties->paid_amount - $oldAmount;
                $newRemainingAmount = $parties->remaining_amount + $oldAmount;

                DB::table('parties')->where('parties_id', $personId)->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemainingAmount,
                ]);
                break;
        }

        // Finally, delete the payment voucher
        DB::table('paymentvoucher')->where('paymentvoucher_id', $id)->delete();

        DB::commit();
        return redirect()->back()->with('success', 'Payment voucher deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

    public function listing(Request $request)
{
    
    
    $query = DB::table('paymentvoucher')
        ->leftJoin('bank', 'paymentvoucher.bank_id', '=', 'bank.bank_id')
        ->select(
            'paymentvoucher.paymentvoucher_id',
            'paymentvoucher.person_type',
            'paymentvoucher.person_id',
            'paymentvoucher.amount',
            'paymentvoucher.narration',
            'paymentvoucher.created_at',
            'bank.bank_name as bank_name' 
        )->orderBy('paymentvoucher.paymentvoucher_id', 'desc');
   

   
    $searchValue = $request->input('search.value');
    if ($searchValue) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('paymentvoucher.narration', 'like', "%$searchValue%")
              ->orWhere('bank.bank_name', 'like', "%$searchValue%")
              ->orWhere('paymentvoucher.created_at', 'like', "%$searchValue%");
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
        case 'Employee':
            return Employee::where('employee_id', $personId)->first(['name']);
        default:
            return null;
    }
}

}