<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_unit_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code', 
        'name',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Item()
    {
        return $this->hasMany('App\Model\Item', 'unit_type_id', 'id')->where('is_delete', 0);
    }

}