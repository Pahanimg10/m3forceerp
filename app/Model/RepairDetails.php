<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RepairDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'repair_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'repair_id', 
        'update_date_time', 
        'repair_status_id', 
        'handed_over_taken_over',
        'remarks',  
        'user_id', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Repair()
    {
        return $this->belongsTo('App\Model\Repair', 'repair_id', 'id');
    }
    
    public function RepairStatus()
    {
        return $this->belongsTo('App\Model\RepairStatus', 'repair_status_id', 'id');
    }
    
    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

}