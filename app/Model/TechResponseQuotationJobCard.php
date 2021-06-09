<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseQuotationJobCard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_quotation_job_card';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_quotation_id',
        'tech_response_job_card_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponseQuotation()
    {
        return $this->belongsTo(\App\Model\TechResponseQuotation::class, 'tech_response_quotation_id', 'id');
    }

    public function TechResponseJobCard()
    {
        return $this->belongsTo(\App\Model\TechResponseJobCard::class, 'tech_response_job_card_id', 'id');
    }
}
