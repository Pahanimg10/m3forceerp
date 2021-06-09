<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inventory_type';
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
        return $this->hasMany('App\Model\InventoryRegister', 'inventory_type_id', 'id')->where('is_delete', 0);
    }
}
