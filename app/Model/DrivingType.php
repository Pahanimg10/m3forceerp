<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DrivingType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'driving_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
    ];

    /**
     * Relations
     */
    public function TechnicalTeamDrivingDetail()
    {
        return $this->hasMany(\App\Model\TechnicalTeamDrivingDetail::class, 'driving_type_id', 'id')->where('is_delete', 0);
    }
}
