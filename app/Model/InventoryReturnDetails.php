<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryReturnDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inventory_return_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inventory_return_id', 
        'inventory_register_id',
        'remarks',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function InventoryReturn()
    {
        return $this->belongsTo('App\Model\InventoryReturn', 'inventory_return_id', 'id');
    }
    
    public function InventoryRegister()
    {
        return $this->belongsTo('App\Model\InventoryRegister', 'inventory_register_id', 'id');
    }

}