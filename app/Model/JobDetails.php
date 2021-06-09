<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'job_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'job_id', 
        'update_date_time', 
        'job_status_id', 
        'job_scheduled_date_time',
        'start_date',
        'end_date',
        'remarks',  
        'user_id', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Job()
    {
        return $this->belongsTo('App\Model\Job', 'job_id', 'id');
    }
    
    public function JobStatus()
    {
        return $this->belongsTo('App\Model\JobStatus', 'job_status_id', 'id');
    }
    
    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

}