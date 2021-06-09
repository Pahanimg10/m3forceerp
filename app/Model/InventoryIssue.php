<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryIssue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inventory_issue';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inventory_issue_no',
        'inventory_issue_date_time',
        'issued_to',
        'inventory_issue_value',
        'remarks',
        'is_posted',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function InventoryIssueDetails()
    {
        return $this->hasMany(\App\Model\InventoryIssueDetails::class, 'inventory_issue_id', 'id')->where('is_delete', 0);
    }

    public function InventoryReturn()
    {
        return $this->hasMany(\App\Model\InventoryReturn::class, 'inventory_issue_id', 'id')->where('is_delete', 0);
    }
}
