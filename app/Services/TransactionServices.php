<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use DB;

class TransactionServices
{
    public static function generatePenyebut($nilai)
    {
        $nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$result = "";
		if ($nilai < 12) {
			$result = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$result = TransactionServices::generatePenyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$result = TransactionServices::generatePenyebut($nilai/10)." puluh". TransactionServices::generatePenyebut($nilai % 10);
		} else if ($nilai < 200) {
			$result = " seratus" . TransactionServices::generatePenyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$result = TransactionServices::generatePenyebut($nilai/100) . " ratus" . TransactionServices::generatePenyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$result = " seribu" . TransactionServices::generatePenyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$result = TransactionServices::generatePenyebut($nilai/1000) . " ribu" . TransactionServices::generatePenyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$result = TransactionServices::generatePenyebut($nilai/1000000) . " juta" . TransactionServices::generatePenyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$result = TransactionServices::generatePenyebut($nilai/1000000000) . " milyar" . TransactionServices::generatePenyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$result = TransactionServices::generatePenyebut($nilai/1000000000000) . " trilyun" . TransactionServices::generatePenyebut(fmod($nilai,1000000000000));
		}     
		return $result;
    }

	public static function getTransactionNumber(){
		$date = Carbon::now()->format('Y/m/d');
		$IdForTransNumber = null;

		$checkTransaction = Transaction::where('transaction_date', $date)->orderBy('transaction_number', 'DESC')->first();
		if ($checkTransaction) {
			// get transaction number from last transaction
			$lastTransactionNumber = $checkTransaction->transaction_number;
			// get last transaction number, and substract 1 to it
			$nextTransactionNumber = (int)substr($lastTransactionNumber, 11) + 1;
		} else {
			// if no transaction found, start from 1
			$nextTransactionNumber = 1;
		}

		// Format transaction number
    	$formattedTransactionNumber =  'INV'.str_replace("/","",$date).str_pad($nextTransactionNumber, 7, '0', STR_PAD_LEFT);

		$data = [
			'date' => $date,
			// 'checkTransaction' => $countChar
			'number' => $formattedTransactionNumber
		];

		return $data;

	}

	function customRound($price) {
		$integerPart = floor($price);
		$decimalPart = $price - $integerPart;
		
		if ($decimalPart < 0.5) {
			return $integerPart;
		} else {
			return ceil($price);
		}
	}

	function saveTransaction(request $request) {

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
                $sum_total_item = 0;
                
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
                $transaction->creator = auth()->user()->username;
                
                foreach($items as $item){
                    
                    // Save every items in the transaction to db
                    $transactionDetail = new TransactionDetail();
                    $transactionDetail->transaction_number = $request->transaction_number;
                    $transactionDetail->item_code = $item->item_code;
                    $transactionDetail->item_name = $item->item_name;
                    $transactionDetail->item_quantity = $item->item_quantity;
                    $transactionDetail->item_quantity_unit = $item->item_quantity_unit;
                    $transactionDetail->item_price = $item->item_price;
                    $transactionDetail->item_total_price = $item->item_total_price;
                    $transactionDetail->disc_percent = $item->disc_percent;
                    
                    $discount_percentage += floatval($item->disc_percent);                    
                    $subtotal +=  (intval($item->item_quantity) * intval($item->item_price));
                    $discount_total += ($subtotal * floatval($item->disc_percent) / 100);
                    $sum_total_item += $item->item_quantity;
                    
                    $transactionDetail->save();
                }
                
                $transaction->discount_total = intval($discount_total);
                $transaction->discount_percentage = $discount_percentage;
                $transaction->subtotal = intval($subtotal);
                $transaction->final_total = intval($subtotal - $discount_total);
                $transaction->transaction_status = "Belum Dibayar";
                $transaction->item_total = $sum_total_item;
				// $transaction->details = $items;

                $transaction->save(); 

                return $transaction;
            }
        });

		return $transaction;
	}

	function updateTransaction(Request $request) {
		// $transaction = DB::transaction(function () use ($request){
            // declare new TransactionServices
            $transactionServices = new TransactionServices();

            $items = json_decode($request->items);
            // check items is exist before execute process
            if(count($items) != 0){
                

                // declare variable
                $discount_percentage = 0;
                $discount_total = 0;
                $subtotal = 0;
                $discount_percentage = 0;
                $sum_total_item = 0;

                if(!empty($request->deleted_items)) {
                    $deletedItemsIds = json_decode($request->deleted_items);
                    $deletedItems = TransactionDetail::where('transaction_number',$request->transaction_number)->whereIn('id', $deletedItemsIds)->delete();
                }
                
                // Create new transaction object
                $transaction = Transaction::where('transaction_number',$request->transaction_number)
                                ->first();
                
                $transaction->transaction_date = $request->transaction_date;
                $transaction->transaction_number = $request->transaction_number;
                $transaction->sales_code = $request->sales_code;
                $transaction->customer_name = $request->customer_name;
                $transaction->customer_address = $request->customer_address;

            
                foreach($items as $item){
                    
                    // Save every items in the transaction to db
                    if(property_exists($item, 'id')){
                        $transactionDetail = TransactionDetail::where('transaction_number', $item->transaction_number)
                                            ->where('id', $item->id)
                                            ->first();
                    } else {
                        $transactionDetail = new TransactionDetail();
                    }

                    $transactionDetail->transaction_number = $request->transaction_number;
                    $transactionDetail->item_code = $item->item_code;
                    $transactionDetail->item_name = $item->item_name;
                    $transactionDetail->item_quantity = $item->item_quantity;
                    $transactionDetail->item_quantity_unit = $item->item_quantity_unit;
                    $transactionDetail->item_price = $item->item_price;
                    $transactionDetail->item_total_price = $item->item_total_price;
                    $transactionDetail->disc_percent = $item->disc_percent;
                    
                    $discount_percentage += floatval($item->disc_percent);                    
                    $subtotal +=  (intval($item->item_quantity) * intval($item->item_price));
                    $discount_total += ((intval($item->item_quantity) * intval($item->item_price)) * floatval($item->disc_percent) / 100);
                    $sum_total_item += $item->item_quantity;

                    $transactionDetail->save();
                }
                
                $transaction->discount_total = intval($discount_total);
                $transaction->discount_percentage = $discount_percentage;
                $transaction->subtotal = intval($subtotal);
                $transaction->final_total = intval($subtotal - $discount_total);
                $transaction->transaction_status = "Belum Dibayar";
                $transaction->item_total = (float) $sum_total_item;
				// $transaction->details = $items;

                $transaction->save(); 

                return $transaction;
            }
        // });

		// return $transaction;
	}

	function updateTransactionAfterpayment($request, $transaction){

		$payment = DB::transaction(function () use ($request,$transaction){
			if($request->payment_type == "Cash"){
				$transaction->transaction_status = "Sudah Dibayar";
				$transaction->other_fees = (int) $request->other_fees;
				$transaction->return = (int) $request->change;
				$transaction->dp_po = (int) $request->dp_po;
				$transaction->cash = (int) $request->cash;
				$transaction->tax_percentage = (float) $request->tax_percentage;
				$transaction->tax_total = (int) $request->tax_total;
				$transaction->payment_type = $request->payment_type;
				$transaction->final_total_after_additional = (int) $request->final_after_tax_total;
			} 
			$transaction->save();

			return $transaction;
		});


		return $payment;
	}
}