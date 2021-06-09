<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseQuotation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_quotation';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_id',
        'tech_response_quotation_no',
        'tech_response_quotation_date_time',
        'remarks',
        'special_notes',
        'show_brand',
        'show_origin',
        'show_transport',
        'is_currency',
        'usd_rate',
        'installation_charge',
        'transport_charge',
        'attendance_fee',
        'tech_response_quotation_value',
        'is_confirmed',
        'user_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponse()
    {
        return $this->belongsTo(\App\Model\TechResponse::class, 'tech_response_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }

    public function TechResponseQuotationDiscount()
    {
        return $this->hasMany(\App\Model\TechResponseQuotationDiscount::class, 'tech_response_quotation_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseQuotationJobCard()
    {
        return $this->hasMany(\App\Model\TechResponseQuotationJobCard::class, 'tech_response_quotation_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseCustomerInvoice()
    {
        return $this->hasMany(\App\Model\TechResponseCustomerInvoice::class, 'tech_response_quotation_id', 'id')->where('is_delete', 0);
    }
}
