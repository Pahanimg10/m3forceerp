<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseQuotationDiscount extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_quotation_discount';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_quotation_id',
        'discount_type_id',
        'description',
        'percentage',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponseQuotation()
    {
        return $this->belongsTo('App\Model\TechResponseQuotation', 'tech_response_quotation_id', 'id');
    }

    public function DiscountType()
    {
        return $this->belongsTo('App\Model\DiscountType', 'discount_type_id', 'id');
    }
}
