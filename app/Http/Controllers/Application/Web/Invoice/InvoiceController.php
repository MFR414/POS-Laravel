<?php

namespace App\Http\Controllers\Application\Web\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\TransactionServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF, DB;

class InvoiceController extends Controller
{
    /**
     * Generate invoice
     */
    public function generateInvoice(string $id)
    {
        $transaction = Transaction::find($id);
        
        if(!empty($transaction)){
       
            $transactionDetails = TransactionDetail::where('transaction_number', $transaction->transaction_number)->get();

            if(!empty($transactionDetails)){
                //prepare the data
                $transaction->items = $transactionDetails;
                $transaction->formatted_create_date = Carbon::parse($transaction->created_at)->format('d/m/Y H:i');
                $transaction->sales_code = ucwords($transaction->sales_code);
                $transaction->customer_name = ucwords($transaction->customer_name);
                $transaction->customer_address = ucwords($transaction->customer_address);
                $transaction->creator = strtoupper($transaction->creator);

                $transactionServices = new TransactionServices();
                $transaction->terbilang = $transactionServices->generatePenyebut($transaction->final_total_after_additional);

                // convert into PDF format
                $pdf = PDF::loadView('application.invoice.invoice-layout', ['data' => $transaction])
                        ->setPaper([0, 0, 684, 792], 'landscape');
        
                // Define the file name dynamically (e.g., using an order number)
                $fileName = 'invoice_' . $transaction->transaction_number . '.pdf';

                // Ensure the invoices directory exists within the public storage
                if (!Storage::disk('public')->exists('invoices')) {
                    Storage::disk('public')->makeDirectory('invoices');
                }

                // File path
                $filePath = 'invoices/' . $fileName;

                // Check if the file exists
                if (Storage::disk('public')->exists($filePath)) {
                    // Delete the existing file
                    Storage::disk('public')->delete($filePath);
                }

                // Save the PDF to the public directory
                Storage::disk('public')->put('invoices/' . $fileName, $pdf->output());

                // Check if the file exists
                if (Storage::disk('public')->exists('invoices/' . $fileName)) {
                    // Return a response to download the file
                    return Storage::disk('public')->download('invoices/' . $fileName, $fileName);
                } else {
                    // File not found, handle the error
                    // For example, redirect back with an error message
                    return redirect()->back()->with('error', 'File not found!');
                }
            } else {
                dd('test 2');
                return redirect()
                ->back()
                ->with('error_message', 'Item tidak ditemukan. silahkan coba lagi dalam beberapa saat atau hubungi admin');
            }
        } else {
            dd('test 1');
            return redirect()
                ->back()
                ->with('error_message', 'Kesalahan tidak diketahui, Silahkan coba lagi dalam beberapa saat atau hubungi admin');
        }
    }

    /**
     * check layout invoice
     */

    public function checkInvoicelayout(string $id){
        $transaction = Transaction::find($id);
        $transactionDetails = TransactionDetail::where('transaction_number', $transaction->transaction_number)->get();
        $transaction->items = $transactionDetails;
        
        $transaction->formatted_create_date = Carbon::parse($transaction->created_at)->format('d/m/Y H:i');
        $transaction->sales_code = ucwords($transaction->sales_code);
        $transaction->customer_name = ucwords($transaction->customer_name);
        $transaction->customer_address = ucwords($transaction->customer_address);
        $transaction->creator = strtoupper($transaction->creator);

        $transactionServices = new TransactionServices();
        $transaction->terbilang = $transactionServices->generatePenyebut($transaction->final_total_after_additional);
        // dd($transaction);
        
        return view('application.invoice.invoice-layout', ['data' => $transaction]);
    }
}
