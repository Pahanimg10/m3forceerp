<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'job_status';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'show_update'
    ];

    /**
     * Relations
     */
    
    public function JobDetails()
    {
        return $this->hasMany('App\Model\JobDetails', 'job_status_id', 'id')->where('is_delete', 0);
    }

}