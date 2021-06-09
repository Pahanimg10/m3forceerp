<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CGroupTax extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_c_group_tax';
    protected $primaryKey = 'id';
    protected $fillable = [
        'group_id', 
        'tax_id', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function CGroup()
    {
        return $this->belongsTo('App\Model\CGroup', 'group_id', 'id');
    }
    
    public function CTaxType()
    {
        return $this->belongsTo('App\Model\CTaxType', 'tax_id', 'id');
    }

}