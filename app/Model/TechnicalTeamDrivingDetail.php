<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechnicalTeamDrivingDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_technical_team_driving_detail';
    protected $primaryKey = 'id';
    protected $fillable = [
        'technical_team_id',
        'driving_type_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechnicalTeam()
    {
        return $this->belongsTo(\App\Model\TechnicalTeam::class, 'technical_team_id', 'id');
    }

    public function DrivingType()
    {
        return $this->belongsTo(\App\Model\DrivingType::class, 'driving_type_id', 'id');
    }
}
