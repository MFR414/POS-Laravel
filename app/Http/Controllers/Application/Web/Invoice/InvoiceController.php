<?php

namespace App\Http\Controllers\Application\Web\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Generate invoice
     */
    public function generateInvoice(Request $request)
    {
        $transaction = Transaction::where('transaction_number', $request->transaction_number)->first();
        $transactionDetails = TransactionDetail::where('transaction_number', $request->transaction_number)->get();
        $transaction->items = $transactionDetails;
    
        if(empty($transaction)){
            $pdf = PDF::loadView('application.invoice.invoice-layout', ['data' => $transaction]);
    
            // Define the file name dynamically (e.g., using an order number)
            $fileName = 'invoice_' . $transaction->transaction_number . '.pdf';
    
            // Save the PDF to the public directory
            $pdf->save(public_path('invoices/' . $fileName));
    
            // Optionally, you can return a download response
            return response()->download(public_path('invoices/' . $fileName));
        }
    }

    /**
     * check layout invoice
     */

    public function checkInvoicelayout(string $id){
        $transaction = Transaction::find($id);
        $transactionDetails = TransactionDetail::where('transaction_number', $transaction->transaction_number)->get();
        $transaction->items = $transactionDetails;
        
        return view('application.invoice.invoice-layout', ['data' => $transaction]);
    }
}
