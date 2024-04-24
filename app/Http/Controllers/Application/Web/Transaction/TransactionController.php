<?php

namespace App\Http\Controllers\Application\Web\Transaction;

use App\Http\Controllers\Controller;
use App\Models\TransactionDetail;
use App\Models\Transaction;
use App\Services\TransactionServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::orderBy('transaction_date', 'desc');

        if(!empty($request->transaction_number)){
            $transactions = $transactions->where('transaction_number', 'LIKE', '%'.$request->transaction_number.'%');
        }

        if(!empty($request->customer_name)){
            $transactions = $transactions->where('customer_name', 'LIKE', '%'.$request->customer_name.'%');
        }

        $transactions = $transactions->paginate(20);

        return view('application.transaction.index',[
            'active_page' => 'transactions',
            'transactions' => $transactions,
            'search_terms' => [
                'transaction_number' => $request->transaction_number,
                'customer_name' => $request->customer_name
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $transactionNumber = TransactionServices::getTransactionNumber();
        $date = Carbon::now()->format('Y-m-d');
        return view('application.transaction.create',[
            'active_page' => 'transactions-create',
            'transaction_date' => $date,
            'transaction_number' => $transactionNumber['number']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validate
         $validation_rules = [
            'customer_name' => 'required',
        ];

        $this->validate($request,$validation_rules);

        $items = json_decode($request->items);
        
        $transaction = DB::transaction(function () use ($request,$items){
            // declare new TransactionServices
            $transactionServices = new TransactionServices();

            // check items is exist before execute process
            if(count($items) != 0){

                // declare variable
                $discount_percentage = 0;
                $discount_total = 0;
                $subtotal = 0;
                $discount_percentage = 0;
                
                // Create new transaction object
                $transaction = Transaction::where('transaction_number',$request->transaction_number)->first();

                if(empty($transaction)){
                    $transaction = new Transaction();
                }
                
                $transaction->transaction_date = $request->transaction_date;
                $transaction->transaction_number = $request->transaction_number;
                $transaction->sales_code = $request->sales_code;
                $transaction->customer_name = $request->customer_name;
                $transaction->customer_address = $request->customer_address;
                $transaction->item_total = count($items);
                
                foreach($items as $item){
                    
                    // Save every items in the transaction to db
                    $transactionDetail = new TransactionDetail();
                    $transactionDetail->transaction_number = $request->transaction_number;
                    $transactionDetail->item_code = $item->item_code;
                    $transactionDetail->item_name = $item->item_name;
                    $transactionDetail->item_quantity = $item->item_quantity;
                    $transactionDetail->item_quantity_unit = $item->item_quantity_unit;
                    $transactionDetail->item_price = $item->item_price;
                    $transactionDetail->item_total_price = $item->item_price;
                    
                    $discount_percentage += $item->disc_percent;                    
                    $discount_total += ($item->disc_percent * $item->item_total_price / 100);
                    $subtotal += ($item->item_quantity * $item->item_total_price);
                    
                    $transactionDetail->save();
                }
                
                // dd($items);
                
                $transaction->discount_total = $transactionServices->customRound($discount_total);
                $transaction->discount_percentage = $discount_percentage;
                $transaction->subtotal = $transactionServices->customRound($subtotal);
                $transaction->final_total = $transactionServices->customRound($subtotal - $discount_total);
                $transaction->is_paid = false;

                $transaction->save(); 

                return $transaction;
            }
        });

        return redirect()
        ->route('application.transactions.index')
        ->with('success_message', 'Berhasil menambahkan transaksi '.$transaction->transaction_number.' !');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for submit payment.
     * 
     */
    public function showPaymentForm(string $id){
        $transaction = Transaction::find($id);

        return view('application.transaction.payment',[
            'active_page' => 'transactions-create',
            'transaction' => $transaction
        ]);
    }
}
