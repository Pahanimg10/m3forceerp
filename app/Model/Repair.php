<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'repair';
    protected $primaryKey = 'id';
    protected $fillable = [
        'repair_type_id', 
        'document_id',
        'repair_no', 
        'repair_date_time', 
        'received_from',
        'item_id',
        'model_no', 
        'brand',
        'serial_no',
        'remarks', 
        'user_id', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function RepairType()
    {
        return $this->belongsTo('App\Model\ItemIssueType', 'repair_type_id', 'id');
    }
    
    public function Job()
    {
        return $this->belongsTo('App\Model\Job', 'document_id', 'id');
    }
    
    public function TechResponse()
    {
        return $this->belongsTo('App\Model\TechResponse', 'document_id', 'id');
    }
    
    public function Item()
    {
        return $this->belongsTo('App\Model\Item', 'item_id', 'id');
    }
    
    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
    
    public function RepairDetails()
    {
        return $this->hasMany('App\Model\RepairDetails', 'repair_id', 'id')->where('is_delete', 0);
    }

}