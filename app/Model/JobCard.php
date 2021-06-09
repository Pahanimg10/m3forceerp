<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'job_card';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inquiry_id',
        'job_card_no',
        'job_card_date_time',
        'remarks',
        'job_card_value',
        'is_used',
        'user_id',
        'is_ordered',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Inquiry()
    {
        return $this->belongsTo('App\Model\Inquiry', 'inquiry_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

    public function JobCardDetails()
    {
        return $this->hasMany('App\Model\JobCardDetails', 'job_card_id', 'id')->where('is_delete', 0);
    }

    public function QuotationJobCard()
    {
        return $this->hasMany('App\Model\QuotationJobCard', 'job_card_id', 'id')->where('is_delete', 0);
    }

    public function GoodRequestDocument()
    {
        return $this->hasMany('App\Model\GoodRequestDocument', 'document_id', 'id')->where('is_delete', 0);
    }
}
