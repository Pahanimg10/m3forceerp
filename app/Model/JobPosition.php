<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_job_position';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'code',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function User()
    {
        return $this->hasMany('App\Model\User', 'job_position_id', 'id')->where('is_delete', 0);
    }
}
