<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseJobCard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_job_card';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_id', 
        'tech_response_job_card_no', 
        'tech_response_job_card_date_time',  
        'remarks', 
        'tech_response_job_card_value', 
        'is_used', 
        'user_id', 
        'is_ordered',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function TechResponse()
    {
        return $this->belongsTo('App\Model\TechResponse', 'tech_response_id', 'id');
    }
    
    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
    
    public function TechResponseJobCardDetails()
    {
        return $this->hasMany('App\Model\TechResponseJobCardDetails', 'tech_response_job_card_id', 'id')->where('is_delete', 0);
    }
    
    public function TechResponseQuotationJobCard()
    {
        return $this->hasMany('App\Model\TechResponseQuotationJobCard', 'tech_response_job_card_id', 'id')->where('is_delete', 0);
    }
    
    public function GoodRequestDocument()
    {
        return $this->hasMany('App\Model\GoodRequestDocument', 'document_id', 'id')->where('is_delete', 0);
    }

}