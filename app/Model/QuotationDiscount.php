<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuotationDiscount extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'quotation_discount';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quotation_id',
        'discount_type_id',
        'description',
        'percentage',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Quotation()
    {
        return $this->belongsTo('App\Model\Quotation', 'quotation_id', 'id');
    }

    public function DiscountType()
    {
        return $this->belongsTo('App\Model\DiscountType', 'discount_type_id', 'id');
    }
}
