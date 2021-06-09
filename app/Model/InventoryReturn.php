<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryReturn extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inventory_return';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inventory_issue_id',
        'inventory_return_no',
        'inventory_return_date_time',
        'remarks',
        'inventory_return_value',
        'is_posted',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function InventoryIssue()
    {
        return $this->belongsTo(\App\Model\InventoryIssue::class, 'inventory_issue_id', 'id');
    }

    public function InventoryReturnDetails()
    {
        return $this->hasMany(\App\Model\InventoryReturnDetails::class, 'inventory_return_id', 'id')->where('is_delete', 0);
    }
}
