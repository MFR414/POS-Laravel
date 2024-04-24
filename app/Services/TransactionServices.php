<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Transaction;

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

		// if(empty($checkTransaction)){
		// 	$IdForTransNumber = '0000001';
		// } else {
		// 	$IdForTransNumber = '000000'.$checkTransaction->id;
		// 	$countChar = count_chars($checkTransaction);
			
		// 	// if($countChar > 7){
		// 	// 	$different = $countChar - 7;
		// 	// 	for ($i = 0; $i <= $different ; $i++) { 
		// 	// 		trim($IdForTransNumber. "0");
		// 	// 	}
		// 	// }
		// }

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
}