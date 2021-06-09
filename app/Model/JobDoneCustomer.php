<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobDoneCustomer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'job_done_customer';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'update_date',
        'pending_amount',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->belongsTo('App\Model\Contact', 'contact_id', 'id');
    }

    public function JobDoneCustomerInvoice()
    {
        return $this->hasMany('App\Model\JobDoneCustomerInvoice', 'job_done_customer_id', 'id')->where('is_delete', 0);
    }

    public function JobDoneCustomerPayment()
    {
        return $this->hasMany('App\Model\JobDoneCustomerPayment', 'job_done_customer_id', 'id')->where('is_delete', 0);
    }
}
