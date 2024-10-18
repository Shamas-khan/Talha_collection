<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use DB;
class BankController extends Controller
{

    
    

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
                    bl.bank_ledger_id,
                    bl.debit,
                    bl.credit,
                    bl.bank_id,
                    bl.paymentvoucher_id,
                    bl.reciptvoucher_id,
                    bl.created_at,
                    bl.status,
                    bl.narration,
                    @balance := @balance + bl.credit - bl.debit AS running_balance
                FROM
                    (SELECT @balance := ?) AS var_init,
                    bank_ledger AS bl
                WHERE
                    bl.bank_id = ?
                    " . ($searchValue ? "AND (bl.narration LIKE ? OR DATE(bl.created_at) LIKE ? OR bl.status LIKE ? )" : "") . "
                ORDER BY
                    bl.bank_ledger_id ASC
            )
            SELECT *
            FROM RunningBalance
            ORDER BY bank_ledger_id DESC
            LIMIT ?, ?
        ";
    
       
        $bindings = [
            $initialBalance = 0,
            $id,
        ];
    
        if ($searchValue) {
            $bindings[] = '%' . $searchValue . '%';
            $bindings[] = '%' . $searchValue . '%';
            $bindings[] = '%' . $searchValue . '%';
        }
    
        $bindings[] = (int) $start;
        $bindings[] = (int) $length;
    
        // Execute the query
        $results = DB::select($query, $bindings);
    
        // Calculate total records
        $totalRecords = DB::table('bank_ledger')
            ->where('bank_id', $id)
            ->when($searchValue, function ($query, $searchValue) {
                return $query->where(function ($query) use ($searchValue) {
                    $query->where('narration', 'like', "%$searchValue%")
                        ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%")
                        ->orWhere('status', 'like', "%$searchValue%");
                });
            })
            ->count();
    
        // Prepare the response data
        $data = [
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $results,
        ];
    
        // Return JSON response
        return response()->json($data);
    } 
    

    

    
    public function ledger(String $id)
    {
        $bank = Bank::find($id);
        return view('accounts.banks.ledger', compact('bank'));
    }
    public function index()
    {
        $banks = Bank::all();
        return view('accounts.banks.list', compact('banks'));
    }

    
    public function create()
    {
        return view('accounts.banks.add');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'branch_code' => 'required|string|max:255|unique:bank',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'opening_balance' => 'required|numeric',
        ]);
    
       
        $bank = new Bank();
        $bank->branch_code = $request->branch_code;
        $bank->bank_name = $request->bank_name;
        $bank->account_number = $request->account_number;
        $bank->opening_balance = $request->opening_balance;
        $bank->running_balance = $request->opening_balance; 
        $bank->save();


        DB::table('bank_ledger')->insert([
            'status' => 'Opening',
            'narration' => 'Opening',
            'credit' => $request->opening_balance,
            'bank_id' =>$bank->bank_id,
            'running_balance' => $request->opening_balance,
            
            
        ]);
    
        return redirect()->route('banks.index')->with('success', 'Bank created successfully.');
    }
    

   
    public function show(Bank $bank)
    {
        // return view('banks.show', compact('bank'));
    }

    // Show the form for editing the specified resource.
    public function edit(Bank $bank)
    {
        // return view('banks.edit', compact('bank'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Bank $bank)
    {
        // $request->validate([
        //     'bank_name' => 'required|string|max:255',
        //     'account_number' => 'required|string|max:255',
        //     'opening_balance' => 'required|numeric',
        //     'created_at' => 'required|date',
        //     'updated_at' => 'required|date',
        // ]);

        // $bank->update($request->all());

        // return redirect()->route('banks.index')->with('success', 'Bank updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Bank $bank)
    {
        // $bank->delete();

        // return redirect()->route('banks.index')->with('success', 'Bank deleted successfully.');
    }

   

    public function listing(Request $request)
    {
        // Get the draw parameter
        $draw = $request->input('draw'); 
    
        // Initialize the query builder
        $query = Bank::query();
    
        
        $query->orderBy('bank_id', 'desc');
    
        // Apply searching
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('bank_name', 'like', "%$searchValue%");
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
        $Bank = $query->get();
    
        // Prepare the response data
        $data = [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords, // Total records without filtering
            'recordsFiltered' => $totalRecords, // Total records after filtering (for simplicity, update this based on actual filtering)
            'data' => $Bank, // Data to be displayed in DataTables
        ];
     
        // Return the response as JSON
        return response()->json($data);
    }
}
