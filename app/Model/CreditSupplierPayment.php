<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CreditSupplierPayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'credit_supplier_payment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'credit_supplier_id',  
        'payment_mode_id',
        'payment_no',
        'payment_date_time',
        'amount',
        'cheque_no',
        'bank',
        'remarks',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function CreditSupplier()
    {
        return $this->belongsTo('App\Model\CreditSupplier', 'credit_supplier_id', 'id');
    }
    
    public function PaymentMode()
    {
        return $this->belongsTo('App\Model\PaymentMode', 'payment_mode_id', 'id');
    }

}