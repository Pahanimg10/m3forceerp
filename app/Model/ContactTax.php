<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ContactTax extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_contact_tax';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'tax_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->belongsTo(\App\Model\Contact::class, 'contact_id', 'id');
    }

    public function CTaxType()
    {
        return $this->belongsTo(\App\Model\CTaxType::class, 'tax_id', 'id');
    }
}
