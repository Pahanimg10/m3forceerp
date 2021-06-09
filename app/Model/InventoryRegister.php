<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryRegister extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inventory_register';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'inventory_type_id',
        'inventory_location_id',
        'name',
        'model_no',
        'imei',
        'serial_no',
        'credit_limit',
        'remarks',
        'is_issued',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function InventoryType()
    {
        return $this->belongsTo(\App\Model\InventoryType::class, 'inventory_type_id', 'id');
    }

    public function InventoryLocation()
    {
        return $this->belongsTo(\App\Model\InventoryLocation::class, 'inventory_location_id', 'id');
    }

    public function InventoryIssueDetails()
    {
        return $this->hasMany(\App\Model\InventoryIssueDetails::class, 'inventory_register_id', 'id')->where('is_returned', 0)->where('is_delete', 0);
    }

    public function InventoryReturnDetails()
    {
        return $this->hasMany(\App\Model\InventoryReturnDetails::class, 'inventory_register_id', 'id')->where('is_delete', 0);
    }
}
