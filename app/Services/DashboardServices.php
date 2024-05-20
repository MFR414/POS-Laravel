<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use DB;

class DashboardServices
{

    public function getDashboardData() {
        $transaction = $this->getTransactionData();
        $income = $this->getIncomeData();
        $mostSellingItems = $this->getMostSellingItem();
        $totalSelledItems = $this->getTotalSelledItem();

        return $data = [
            'transaction' => $transaction,
            'income' => $income,
            'most_selling_items' => $mostSellingItems,
            'total_selled_items' => $totalSelledItems
        ];
    }

    public function getTransactionData() {
        $transaction = [];
        $transaction['count_all_time'] = Transaction::where('transaction_status', 'Sudah Dibayar')->count();

        $labels = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $data = [];
        $currentYear = Carbon::now()->year;

        for ($i = 1; $i <= 12; $i++) { // Loop from 1 to 12 for months
            // Create the start and end dates for the month
            $startDate = Carbon::create()->day(1)->month($i)->year($currentYear)->startOfMonth();
            $endDate = Carbon::create()->day(1)->month($i)->year($currentYear)->endOfMonth();
        
            // Count the transactions for the current month
            $transactionCount = Transaction::where('transaction_status', 'Sudah Dibayar')
                                            ->whereMonth('transaction_date', $i)
                                            ->whereYear('transaction_date', $currentYear)
                                            ->count();
        
            // Add the count to the data array
            $data[] = $transactionCount;
        }

        $transaction['labels'] = $labels;
        $transaction['data'] = $data;
        $transaction['year'] = $currentYear;
        return $transaction;
    }

    public function getIncomeData(){
        $income = [];
        $income['sum_all_time'] = Transaction::where('transaction_status', 'Sudah Dibayar')->sum('final_total_after_additional');
        $income['sum_all_time'] = number_format($income['sum_all_time'], 0, ',', '.');

        $labels = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $data = [];
        $currentYear = Carbon::now()->year;

        for ($i = 1; $i <= 12; $i++) { // Loop from 1 to 12 for months
            // Create the start and end dates for the month
            $startDate = Carbon::create()->day(1)->month($i)->year($currentYear)->startOfMonth();
            $endDate = Carbon::create()->day(1)->month($i)->year($currentYear)->endOfMonth();
        
            // Count the transactions for the current month
            $incomeSum = Transaction::where('transaction_status', 'Sudah Dibayar')
                                            ->whereMonth('transaction_date', $i)
                                            ->whereYear('transaction_date', $currentYear)
                                            ->sum('final_total_after_additional');
        
            // Add the count to the data array
            // $data[] = number_format($incomeSum, 0, ',', '.'); 
            $data[] = $incomeSum;
        }

        $income['labels'] = $labels;
        $income['data'] = $data;
        $income['year'] = $currentYear;
        return $income;
    }

    public function getMostSellingItem() {
        $mostSellingItemsArray = [];

        // get 10 most selling items
        $mostSellingItems = TransactionDetail::select('item_name', DB::raw('count(*) as total_sold'))
                        ->groupBy('item_name')
                        ->orderBy('total_sold', 'desc')
                        ->take(10)
                        ->get();

        $mostSellingItemsArray['items_name'] = $mostSellingItems->pluck('item_name')->toArray();
        $mostSellingItemsArray['items_count'] = $mostSellingItems->pluck('total_sold')->toArray();
        
        return $mostSellingItemsArray;
    }

    public function getTotalSelledItem() {
        $totalSelledItemsArray = [];

        // get 10 most selling items
        $totalSelledItems = DB::table('transaction_details')
                            ->select('item_name', 'item_quantity_unit', DB::raw('SUM(item_quantity) as total_item_sold'))
                            ->whereNull('deleted_at')
                            ->groupBy('item_name', 'item_quantity_unit')
                            ->orderBy('total_item_sold', 'desc')
                            ->limit(50)
                            ->get();

        $totalSelledItemsArray['items_name'] = $totalSelledItems->map(function ($item) {
            return $item->item_name . ' (' . $item->item_quantity_unit . ')';
        })->toArray();

        $totalSelledItemsArray['items_count'] = $totalSelledItems->pluck('total_item_sold')->toArray();
        
        return $totalSelledItemsArray;
    }
}