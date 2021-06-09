<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobDoneCustomerInvoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'job_done_customer_invoice';
    protected $primaryKey = 'id';
    protected $fillable = [
        'job_done_customer_id',  
        'quotation_id',
        'invoice_date',
        'invoice_no',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function JobDoneCustomer()
    {
        return $this->belongsTo('App\Model\JobDoneCustomer', 'job_done_customer_id', 'id');
    }
    
    public function Quotation()
    {
        return $this->belongsTo('App\Model\Quotation', 'quotation_id', 'id');
    }

}