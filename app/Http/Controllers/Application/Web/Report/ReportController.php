<?php

namespace App\Http\Controllers\Application\Web\Report;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request){

        $transactions =  Transaction::where('transaction_status','Sudah Dibayar')->orderBy('transaction_date', 'desc');

        // Filter by month
        if (!empty($request->month)) {
            $transactions->whereMonth('transaction_date', $request->month);
        }

        // Filter by year
        if (!empty($request->year)) {
            $transactions->whereYear('transaction_date', $request->year);
        }

        // // Filter by date
        // if (!empty($request->date)) {
        //     $transactions->whereDate('transaction_date', $request->date);
        // }

        $transactions = $transactions->paginate(20);
        foreach ($transactions as $transaction) {
            $transactionDetails = TransactionDetail::where('transaction_number',$transaction->transaction_number)->get();
            $transaction->details = $transactionDetails;
        }

        return view('application.report.index', [
            'active_page' => 'report',
            'transactions' => $transactions,
            'search_terms' => [
                'transaction_month' => $request->month,
                'transaction_year' => $request->year,
                'transaction_date' => $request->date
            ]
        ]);
    }

    public function detail(string $id){
        
    }
    public function generateReport(Request $request)
    {
        // Get filter criteria from the request
        $month = $request->input('month');
        $year = $request->input('year');

        // Initialize the query
        $query = Transaction::query();

        // Apply filters if provided
        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }
        if ($year) {
            $query->whereYear('transaction_date', $year);
        }

        // Execute the query and get the results
        $transactions = $query->get();

        // Share data to view
        $data = [
            'transactions' => $transactions,
            'search_terms' => [
                'transaction_month' => $month,
                'transaction_year' => $year,
            ],
        ];

        // Load view and pass data to it
        $pdf = PDF::loadView('application.reports.report-pdf', $data);

        // Download the PDF file
        return $pdf->download('transaction_report.pdf');
    }
}
