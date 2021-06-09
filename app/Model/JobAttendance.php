<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobAttendance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'job_attendance';
    protected $primaryKey = 'id';
    protected $fillable = [
        'attended_date',
        'technical_team_id',
        'job_type_id',
        'job_id',
        'mandays',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechnicalTeam()
    {
        return $this->belongsTo(\App\Model\TechnicalTeam::class, 'technical_team_id', 'id');
    }

    public function JobType()
    {
        return $this->belongsTo(\App\Model\ItemIssueType::class, 'job_type_id', 'id');
    }

    public function Job()
    {
        return $this->belongsTo(\App\Model\Job::class, 'job_id', 'id');
    }

    public function TechResponse()
    {
        return $this->belongsTo(\App\Model\TechResponse::class, 'job_id', 'id');
    }
}
