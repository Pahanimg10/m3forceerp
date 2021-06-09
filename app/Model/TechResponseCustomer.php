<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseCustomer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_customer';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',  
        'update_date',
        'pending_amount',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Contact()
    {
        return $this->belongsTo('App\Model\Contact', 'contact_id', 'id');
    }
    
    public function TechResponseCustomerInvoice()
    {
        return $this->hasMany('App\Model\TechResponseCustomerInvoice', 'tech_response_customer_id', 'id')->where('is_delete', 0);
    }
    
    public function TechResponseCustomerPayment()
    {
        return $this->hasMany('App\Model\TechResponseCustomerPayment', 'tech_response_customer_id', 'id')->where('is_delete', 0);
    }

}