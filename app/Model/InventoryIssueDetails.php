<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryIssueDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inventory_issue_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inventory_issue_id', 
        'inventory_register_id',
        'remarks',
        'is_returned',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function InventoryIssue()
    {
        return $this->belongsTo('App\Model\InventoryIssue', 'inventory_issue_id', 'id');
    }
    
    public function InventoryRegister()
    {
        return $this->belongsTo('App\Model\InventoryRegister', 'inventory_register_id', 'id');
    }

}