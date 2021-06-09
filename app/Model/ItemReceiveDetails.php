<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemReceiveDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'item_receive_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'item_receive_id',
        'item_id',
        'quantity',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function ItemReceive()
    {
        return $this->belongsTo('App\Model\ItemReceive', 'item_receive_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo('App\Model\Item', 'item_id', 'id');
    }

    public function ItemReceiveBreakdown()
    {
        return $this->hasMany('App\Model\ItemReceiveBreakdown', 'item_receive_detail_id', 'id')->where('is_delete', 0);
    }
}
