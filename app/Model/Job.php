<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'job';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inquiry_id',
        'job_no',
        'job_date_time',
        'job_value',
        'mandays',
        'is_job_scheduled',
        'is_completed',
        'user_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Inquiry()
    {
        return $this->belongsTo(\App\Model\Inquiry::class, 'inquiry_id', 'id');
    }

    public function JobDetails()
    {
        return $this->hasMany(\App\Model\JobDetails::class, 'job_id', 'id')->where('is_delete', 0);
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }

    public function JobAttendance()
    {
        return $this->hasMany(\App\Model\JobAttendance::class, 'job_id', 'id')->where('is_delete', 0);
    }

    public function ItemIssue()
    {
        return $this->hasMany(\App\Model\ItemIssue::class, 'document_id', 'id')->where('is_delete', 0);
    }

    public function PettyCashIssue()
    {
        return $this->hasMany(\App\Model\PettyCashIssue::class, 'document_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany(\App\Model\Repair::class, 'document_id', 'id')->where('is_delete', 0);
    }
}
