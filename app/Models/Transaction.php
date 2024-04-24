<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

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
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_trasaction_date',
    ];

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
}
