<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubItemCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_sub_item_category';
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
        return $this->hasMany('App\Model\Item', 'sub_category_id', 'id')->where('is_delete', 0);
    }

}