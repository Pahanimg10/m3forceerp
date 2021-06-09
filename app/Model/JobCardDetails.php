<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobCardDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'job_card_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'job_card_id',
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
    public function JobCard()
    {
        return $this->belongsTo(\App\Model\JobCard::class, 'job_card_id', 'id');
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
