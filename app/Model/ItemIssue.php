<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemIssue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'item_issue';
    protected $primaryKey = 'id';
    protected $fillable = [
        'item_issue_type_id',
        'document_id',
        'item_issue_no',
        'item_issue_date_time',
        'issued_to',
        'remarks',
        'item_issue_value',
        'is_posted',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function ItemIssueType()
    {
        return $this->belongsTo(\App\Model\ItemIssueType::class, 'item_issue_type_id', 'id');
    }

    public function Job()
    {
        return $this->belongsTo(\App\Model\Job::class, 'document_id', 'id');
    }

    public function TechResponse()
    {
        return $this->belongsTo(\App\Model\TechResponse::class, 'document_id', 'id');
    }

    public function ItemIssueDetails()
    {
        return $this->hasMany(\App\Model\ItemIssueDetails::class, 'item_issue_id', 'id')->where('is_delete', 0);
    }

    public function ItemReceive()
    {
        return $this->hasMany(\App\Model\ItemReceive::class, 'item_issue_id', 'id')->where('is_delete', 0);
    }
}
