<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'purchase_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name'
    ];

    /**
     * Relations
     */
    
    public function Item()
    {
        return $this->hasMany('App\Model\Item', 'purchase_type_id', 'id')->where('is_delete', 0);
    }

}