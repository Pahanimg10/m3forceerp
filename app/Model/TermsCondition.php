<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TermsCondition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'terms_condition';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relations
     */
    public function QuotationTermsCondition()
    {
        return $this->hasMany(\App\Model\QuotationTermsCondition::class, 'terms_condition_id', 'id')->where('is_delete', 0);
    }
}
