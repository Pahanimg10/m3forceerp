<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuotationTermsCondition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'quotation_terms_condition';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quotation_id',
        'terms_condition_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Quotation()
    {
        return $this->belongsTo(\App\Model\Quotation::class, 'quotation_id', 'id');
    }

    public function TermsCondition()
    {
        return $this->belongsTo(\App\Model\TermsCondition::class, 'terms_condition_id', 'id');
    }
}
