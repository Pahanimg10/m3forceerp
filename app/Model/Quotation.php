<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'quotation';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inquiry_id',
        'quotation_no',
        'quotation_date_time',
        'remarks',
        'special_notes',
        'show_brand',
        'show_origin',
        'show_installation_meters',
        'is_currency',
        'usd_rate',
        'show_excavation_work',
        'show_transport',
        'show_food',
        'show_accommodation',
        'show_bata',
        'show_other_expenses',
        'quotation_value',
        'is_confirmed',
        'user_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Inquiry()
    {
        return $this->belongsTo(\App\Model\Inquiry::class, 'inquiry_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }

    public function QuotationDiscount()
    {
        return $this->hasMany(\App\Model\QuotationDiscount::class, 'quotation_id', 'id')->where('is_delete', 0);
    }

    public function QuotationJobCard()
    {
        return $this->hasMany(\App\Model\QuotationJobCard::class, 'quotation_id', 'id')->where('is_delete', 0);
    }

    public function QuotationCostSheet()
    {
        return $this->hasMany(\App\Model\QuotationCostSheet::class, 'quotation_id', 'id')->where('is_delete', 0);
    }

    public function QuotationTermsCondition()
    {
        return $this->hasMany(\App\Model\QuotationTermsCondition::class, 'quotation_id', 'id')->where('is_delete', 0);
    }

    public function JobDoneCustomerInvoice()
    {
        return $this->hasMany(\App\Model\JobDoneCustomerInvoice::class, 'quotation_id', 'id')->where('is_delete', 0);
    }
}
