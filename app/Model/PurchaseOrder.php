<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'purchase_order';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'good_request_id',
        'purchase_order_no',
        'purchase_order_date_time',
        'remarks',
        'purchase_order_value',
        'is_posted',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->belongsTo(\App\Model\Contact::class, 'contact_id', 'id');
    }

    public function GoodRequest()
    {
        return $this->belongsTo(\App\Model\GoodRequest::class, 'good_request_id', 'id');
    }

    public function PurchaseOrderDetails()
    {
        return $this->hasMany(\App\Model\PurchaseOrderDetails::class, 'purchase_order_id', 'id')->where('is_delete', 0);
    }

    public function GoodReceive()
    {
        return $this->hasOne(\App\Model\GoodReceive::class, 'purchase_order_id', 'id');
    }
}
