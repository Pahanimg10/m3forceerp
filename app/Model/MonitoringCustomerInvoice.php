<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MonitoringCustomerInvoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'monitoring_customer_invoice';
    protected $primaryKey = 'id';
    protected $fillable = [
        'monitoring_customer_id',
        'invoice_date',
        'invoice_no',
        'payment_received',
        'is_settled',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function MonitoringCustomer()
    {
        return $this->belongsTo('App\Model\MonitoringCustomer', 'monitoring_customer_id', 'id');
    }
}
