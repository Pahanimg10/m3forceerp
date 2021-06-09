<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechnicalTeam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_technical_team';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'epf_no',
        'name',
        'contact_no',
        'nic',
        'is_driving',
        'is_active',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechnicalTeamDrivingDetail()
    {
        return $this->hasMany(\App\Model\TechnicalTeamDrivingDetail::class, 'technical_team_id', 'id')->where('is_delete', 0);
    }

    public function JobAttendance()
    {
        return $this->hasMany(\App\Model\JobAttendance::class, 'technical_team_id', 'id')->where('is_delete', 0);
    }
}
