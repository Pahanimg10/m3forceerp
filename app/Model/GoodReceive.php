<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoodReceive extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'good_receive';
    protected $primaryKey = 'id';
    protected $fillable = [
        'purchase_order_id',
        'invoice_no',
        'good_receive_no',
        'good_receive_date_time',
        'remarks',
        'good_receive_value',
        'is_posted',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function PurchaseOrder()
    {
        return $this->belongsTo(\App\Model\PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function GoodReceiveDetails()
    {
        return $this->hasMany(\App\Model\GoodReceiveDetails::class, 'good_receive_id', 'id')->where('is_delete', 0);
    }

    public function CreditSupplierGoodReceive()
    {
        return $this->hasMany(\App\Model\CreditSupplierGoodReceive::class, 'good_receive_id', 'id')->where('is_delete', 0);
    }
}
