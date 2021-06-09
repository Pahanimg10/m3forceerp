<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ContactInvoiceMonth extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_contact_inv_month';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id', 
        'month', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Contact()
    {
        return $this->belongsTo('App\Model\Contact', 'contact_id', 'id');
    }

}