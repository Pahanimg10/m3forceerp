<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CreditSupplierGoodReceive extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'credit_supplier_good_receive';
    protected $primaryKey = 'id';
    protected $fillable = [
        'credit_supplier_id',  
        'good_rececive_id',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function CreditSupplier()
    {
        return $this->belongsTo('App\Model\CreditSupplier', 'credit_supplier_id', 'id');
    }
    
    public function GoodReceive()
    {
        return $this->belongsTo('App\Model\GoodReceive', 'good_rececive_id', 'id');
    }

}