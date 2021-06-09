<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'tech_response_status';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'show_update',
    ];

    /**
     * Relations
     */
    public function TechResponseDetails()
    {
        return $this->hasMany(\App\Model\TechResponseDetails::class, 'tech_response_status_id', 'id')->where('is_delete', 0);
    }
}
