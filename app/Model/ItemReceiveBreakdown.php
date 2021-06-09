<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemReceiveBreakdown extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'item_receive_breakdown';
    protected $primaryKey = 'id';
    protected $fillable = [
        'item_receive_detail_id',
        'type',
        'detail_id',
        'quantity',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function ItemReceiveDetails()
    {
        return $this->belongsTo('App\Model\ItemReceiveDetails', 'item_receive_detail_id', 'id');
    }

    public function GoodReceiveDetails()
    {
        return $this->belongsTo('App\Model\GoodReceiveDetails', 'detail_id', 'id');
    }

    public function GoodReceiveBreakdown()
    {
        return $this->belongsTo('App\Model\GoodReceiveBreakdown', 'detail_id', 'id');
    }
}
