<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'transaction';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'sales_code',
        'customer_name',
        'customer_address',
        'item_total',
        'discount_percentage',
        'discount_total',
        'tax_percentage',
        'tax_total',
        'other_fees',
        'subtotal',
        'final_total',
        'dp_po',
        'cash',
        'credit',
        'debit_card',
        'credit_card',
        'return',
        'transaction_status'
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_transaction_date',
        'formatted_subtotal',
        'formatted_final_total',
        'formatted_discount_total',
    ];
    
    /**
     * Get the formatted item_price attribute.
     *
     * @return string
     */
    public function getFormattedItemPriceAttribute()
    { 
        return 'Rp '.number_format($this->item_price, 0, ',', '.');
    }

    /**
     * Get the formatted subtotal attribute.
     *
     * @return string
     */
    public function getFormattedSubtotalAttribute()
    { 
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get the formatted final_total attribute.
     *
     * @return string
     */
    public function getFormattedFinalTotalAttribute()
    { 
        return 'Rp '.number_format($this->final_total, 0, ',', '.');
    }

     /**
     * Get the formatted final_total attribute.
     *
     * @return string
     */
    public function getFormattedDiscountTotalAttribute()
    { 
        return 'Rp '.number_format($this->discount_total, 0, ',', '.');
    }

    /**
     * Get the formatted transaction_date attribute.
     *
     * @return string
     */
    public function getFormattedTransactionDateAttribute()
    {
        // Format transaction_date attribute to m/d/Y
        $formatted_date = Carbon::parse($this->transaction_date)->format('d/m/Y');
        // return the formatted transaction_date into appends field
        return $formatted_date;
    }

    /**
     * Get the sales that owns the Transaction
     */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_number', 'transaction_number');
    }
}
