<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_id',
        'update_date_time',
        'tech_response_status_id',
        'job_scheduled_date_time',
        'is_chargeable',
        'invoice_no',
        'invoice_value',
        'remarks',
        'user_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponse()
    {
        return $this->belongsTo(\App\Model\TechResponse::class, 'tech_response_id', 'id');
    }

    public function TechResponseStatus()
    {
        return $this->belongsTo(\App\Model\TechResponseStatus::class, 'tech_response_status_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }
}
