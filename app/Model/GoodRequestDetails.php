<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoodRequestDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'good_request_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'good_request_id', 
        'type',
        'detail_id', 
        'document_no',
        'item_id', 
        'rate',
        'quantity',
        'is_ordered',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function GoodRequest()
    {
        return $this->belongsTo('App\Model\GoodRequest', 'good_request_id', 'id');
    }
    
    public function Item()
    {
        return $this->belongsTo('App\Model\Item', 'item_id', 'id');
    }
    
    public function JobCardDetails()
    {
        return $this->belongsTo('App\Model\JobCardDetails', 'detail_id', 'id');
    }
    
    public function InstallationSheetDetails()
    {
        return $this->belongsTo('App\Model\InstallationSheetDetails', 'detail_id', 'id');
    }
    
    public function TechResponseJobCardDetails()
    {
        return $this->belongsTo('App\Model\TechResponseJobCardDetails', 'detail_id', 'id');
    }
    
    public function TechResponseInstallationSheetDetails()
    {
        return $this->belongsTo('App\Model\TechResponseInstallationSheetDetails', 'detail_id', 'id');
    }

}