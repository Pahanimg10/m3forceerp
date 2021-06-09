<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'purchase_order_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'rate',
        'quantity',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function PurchaseOrder()
    {
        return $this->belongsTo(\App\Model\PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo(\App\Model\Item::class, 'item_id', 'id');
    }
}
