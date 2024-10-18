<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use DB;
class PartyController extends Controller
{
    public function ledgerlist(Request $request, string $id)
    {
        
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value');
        $page = $start / $length;
    
       
        $query = "
            WITH RunningBalance AS (
                SELECT
                    bl.party_ledger_id,
                    bl.debit,
                    bl.credit,
                    
                    bl.party_id,
                   
                    bl.paymentvoucher_id,
                    bl.reciptvoucher_id,
                    bl.created_at,
                    bl.status,
                    bl.narration,
                    @balance := @balance + bl.credit - bl.debit AS running_balance,
                     CASE 
                            WHEN bl.credit > 0 THEN 'Cr'
                            WHEN bl.debit > 0 THEN 'Dr'
                            ELSE ''
                        END AS balance_type
                FROM
                    (SELECT @balance := ?) AS var_init,
                    party_ledger AS bl
                WHERE
                    bl.party_id = ?
                    " . ($searchValue ? "AND (bl.narration LIKE ? OR DATE(bl.created_at) LIKE ? OR bl.status LIKE ? )" : "") . "
                ORDER BY
                    bl.party_ledger_id ASC
            )
            SELECT *
            FROM RunningBalance
            ORDER BY party_ledger_id DESC
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
        $totalRecords = DB::table('party_ledger')
            ->where('party_id', $id)
            ->when($searchValue, function ($query, $searchValue) {
                return $query->where(function ($query) use ($searchValue) {
                    $query->where('narration', 'like', "%$searchValue%")
                        ->orWhere(DB::raw('DATE(created_at)'), 'like', "%$searchValue%")
                        ->orWhere('status', 'like', "%$searchValue%");
                });
            })
            ->count();
            foreach ($results as &$result) {
                $result->running_balance .= ' ' . $result->balance_type;
            }
        // Prepare the response data
        $data = [
            'draw' => (int) $request->input('draw',1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $results,
        ];
    
        // Return JSON response
        return response()->json($data);
    } 


    public function ledger(string $id)
    {
        $party = DB::table('parties')
        ->where('parties_id', $id)
        ->first();

        return view('parties.ledger',compact('party'));
    }

    public function index()
    {
        return view('parties.list');
    }

    public function create()
    {
        return view('parties.add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'op_balance' => 'required|string', 
        ]);

        $cleanedOpBalance = str_replace(',', '', $validatedData['op_balance']);

      
        $partyid = DB::table('parties')->insertGetId([
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'opening_balance' => $cleanedOpBalance,
            'total_amount' => $cleanedOpBalance,
            'paid_amount' => 0,
            'remaining_amount' => $cleanedOpBalance,
           
            
        ]);

        DB::table('party_ledger')->insert([
            'party_id' => $partyid,
            'status' => 'Opening',
            'narration' => 'Opening',
            'credit' => $cleanedOpBalance,
        ]);

       

        return redirect()->route('parties.index')
                        ->with('success', 'Party created successfully.');
    }

    public function show(Party $party)
    {
        return view('parties.show', compact('party'));
    }

    public function edit(Party $party)
    {
        return view('parties.edit', compact('party'));
    }

    public function update(Request $request, Party $party)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'opening_balance' => 'required|numeric',
        ]);

        $party->update($request->all());

        return redirect()->route('parties.index')
                        ->with('success', 'Party updated successfully.');
    }

    public function destroy(Party $party)
    {
        $party->delete();

        return redirect()->route('parties.index')
                        ->with('success', 'Party deleted successfully.');
    }

    public function getParties()
    {
        $parties = Party::all();
        return response()->json(['data' => $parties]);
    }

    public function listing(Request $request)
{
    $query = Party::query();

    if ($searchValue = $request->input('search.value')) {
        $query->where('name', 'like', "%$searchValue%");
    }

    $totalRecords = $query->count();

    return response()->json([
        'draw' => (int) $request->input('draw', 1),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $query->orderByDesc('parties_id')
                        ->offset($request->input('start', 0))
                        ->limit($request->input('length', 10))
                        ->get(),
    ]);
}

    
}
