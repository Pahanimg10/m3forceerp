<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DiscountType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'discount_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
    ];

    /**
     * Relations
     */
    public function QuotationDiscount()
    {
        return $this->hasMany('App\Model\QuotationDiscount', 'discount_type_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseQuotationDiscount()
    {
        return $this->hasMany('App\Model\TechResponseQuotationDiscount', 'discount_type_id', 'id')->where('is_delete', 0);
    }
}
