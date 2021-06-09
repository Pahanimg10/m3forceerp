<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoodReceiveDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'good_receive_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'good_receive_id',
        'item_id',
        'model_no',
        'brand',
        'origin',
        'rate',
        'quantity',
        'available_quantity',
        'warranty',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function GoodReceive()
    {
        return $this->belongsTo(\App\Model\GoodReceive::class, 'good_receive_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo(\App\Model\Item::class, 'item_id', 'id');
    }

    public function GoodReceiveBreakdown()
    {
        return $this->hasMany(\App\Model\GoodReceiveBreakdown::class, 'good_receive_detail_id', 'id')->where('is_delete', 0);
    }

    public function ItemIssueBreakdown()
    {
        return $this->hasMany(\App\Model\ItemIssueBreakdown::class, 'detail_id', 'id')->where('is_delete', 0);
    }

    public function ItemReceiveBreakdown()
    {
        return $this->hasMany(\App\Model\ItemReceiveBreakdown::class, 'detail_id', 'id')->where('is_delete', 0);
    }
}
