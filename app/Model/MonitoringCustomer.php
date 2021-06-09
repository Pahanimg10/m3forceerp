<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MonitoringCustomer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'monitoring_customer';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'update_date',
        'pending_amount',
        'is_group',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->belongsTo('App\Model\Contact', 'contact_id', 'id');
    }

    public function CGroup()
    {
        return $this->belongsTo('App\Model\CGroup', 'contact_id', 'id');
    }

    public function MonitoringCustomerInvoice()
    {
        return $this->hasMany('App\Model\MonitoringCustomerInvoice', 'monitoring_customer_id', 'id')->where('is_delete', 0);
    }

    public function MonitoringCustomerPayment()
    {
        return $this->hasMany('App\Model\MonitoringCustomerPayment', 'monitoring_customer_id', 'id')->where('is_delete', 0);
    }
}
