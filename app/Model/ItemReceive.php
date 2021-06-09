<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemReceive extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'item_receive';
    protected $primaryKey = 'id';
    protected $fillable = [ 
        'item_issue_id',
        'item_receive_no', 
        'item_receive_date_time',
        'remarks', 
        'item_receive_value', 
        'is_posted',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function ItemIssue()
    {
        return $this->belongsTo('App\Model\ItemIssue', 'item_issue_id', 'id');
    }
    
    public function ItemReceiveDetails()
    {
        return $this->hasMany('App\Model\ItemReceiveDetails', 'item_receive_id', 'id')->where('is_delete', 0);
    }

}