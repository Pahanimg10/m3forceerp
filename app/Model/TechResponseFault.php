<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseFault extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_tech_response_fault';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code', 
        'name', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function TechResponse()
    {
        return $this->hasMany('App\Model\TechResponse', 'tech_response_fault_id', 'id')->where('is_delete', 0);
    }

}