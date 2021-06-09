<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemIssueDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'item_issue_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'item_issue_id',
        'item_id',
        'quantity',
        'warranty',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function ItemIssue()
    {
        return $this->belongsTo(\App\Model\ItemIssue::class, 'item_issue_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo(\App\Model\Item::class, 'item_id', 'id');
    }

    public function ItemIssueBreakdown()
    {
        return $this->hasMany(\App\Model\ItemIssueBreakdown::class, 'item_issue_detail_id', 'id')->where('is_delete', 0);
    }
}
