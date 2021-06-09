<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryLocation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inventory_location';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Relations
     */
    public function InventoryRegister()
    {
        return $this->hasMany('App\Model\InventoryRegister', 'inventory_location_id', 'id')->where('is_delete', 0);
    }
}
