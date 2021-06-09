<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuotationJobCard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'quotation_job_card';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quotation_id',
        'job_card_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Quotation()
    {
        return $this->belongsTo(\App\Model\Quotation::class, 'quotation_id', 'id');
    }

    public function JobCard()
    {
        return $this->belongsTo(\App\Model\JobCard::class, 'job_card_id', 'id');
    }

    public function QuotationJobCardDetails()
    {
        return $this->hasMany(\App\Model\QuotationJobCardDetails::class, 'quotation_job_card_id', 'id')->where('is_delete', 0);
    }
}
