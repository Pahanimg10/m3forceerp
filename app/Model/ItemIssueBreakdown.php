<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemIssueBreakdown extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'item_issue_breakdown';
    protected $primaryKey = 'id';
    protected $fillable = [
        'item_issue_detail_id',
        'type',
        'detail_id',
        'quantity',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function ItemIssueDetails()
    {
        return $this->belongsTo('App\Model\ItemIssueDetails', 'item_issue_detail_id', 'id');
    }

    public function GoodReceiveDetails()
    {
        return $this->belongsTo('App\Model\GoodReceiveDetails', 'detail_id', 'id');
    }

    public function GoodReceiveBreakdown()
    {
        return $this->belongsTo('App\Model\GoodReceiveBreakdown', 'detail_id', 'id');
    }

    public function Repair()
    {
        return $this->hasMany('App\Model\Repair', 'receive_serial_id', 'id')->where('is_delete', 0);
    }
}
