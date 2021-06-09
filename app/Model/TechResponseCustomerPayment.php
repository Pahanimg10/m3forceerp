<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseCustomerPayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_customer_payment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_customer_id',
        'payment_mode_id',
        'collection_person_id',
        'receipt_no',
        'receipt_date_time',
        'amount',
        'cheque_no',
        'bank',
        'realize_date',
        'remarks',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponseCustomer()
    {
        return $this->belongsTo('App\Model\TechResponseCustomer', 'tech_response_customer_id', 'id');
    }

    public function PaymentMode()
    {
        return $this->belongsTo('App\Model\PaymentMode', 'payment_mode_id', 'id');
    }

    public function CollectionPerson()
    {
        return $this->belongsTo('App\Model\CollectionPerson', 'collection_person_id', 'id');
    }
}
