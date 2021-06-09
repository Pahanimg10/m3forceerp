<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CreditSupplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'credit_supplier';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'update_date',
        'pending_amount',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->belongsTo(\App\Model\Contact::class, 'contact_id', 'id');
    }

    public function CreditSupplierGoodReceive()
    {
        return $this->hasMany(\App\Model\CreditSupplierGoodReceive::class, 'credit_supplier_id', 'id')->where('is_delete', 0);
    }

    public function CreditSupplierPayment()
    {
        return $this->hasMany(\App\Model\CreditSupplierPayment::class, 'credit_supplier_id', 'id')->where('is_delete', 0);
    }
}
