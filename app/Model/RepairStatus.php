<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RepairStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'repair_status';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name'
    ];

    /**
     * Relations
     */
    
    public function RepairDetails()
    {
        return $this->hasMany('App\Model\RepairDetails', 'repair_status_id', 'id')->where('is_delete', 0);
    }

}