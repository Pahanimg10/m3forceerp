<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseJobCardDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_job_card_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_job_card_id',
        'item_id',
        'rate',
        'quantity',
        'margin',
        'is_main',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponseJobCard()
    {
        return $this->belongsTo(\App\Model\TechResponseJobCard::class, 'tech_response_job_card_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo(\App\Model\Item::class, 'item_id', 'id');
    }

    public function GoodRequestDetails()
    {
        return $this->hasMany(\App\Model\GoodRequestDetails::class, 'detail_id', 'id')->where('is_delete', 0);
    }
}
