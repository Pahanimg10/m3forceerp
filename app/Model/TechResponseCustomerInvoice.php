<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseCustomerInvoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_customer_invoice';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_customer_id',  
        'tech_response_quotation_id',
        'invoice_date',
        'invoice_no',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function TechResponseCustomer()
    {
        return $this->belongsTo('App\Model\TechResponseCustomer', 'tech_response_customer_id', 'id');
    }
    
    public function TechResponseQuotation()
    {
        return $this->belongsTo('App\Model\TechResponseQuotation', 'tech_response_quotation_id', 'id');
    }

}