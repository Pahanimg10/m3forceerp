<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerComplainDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'customer_complain_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_complain_id',
        'update_date_time',
        'customer_complain_status_id',
        'remarks',
        'user_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function CustomerComplain()
    {
        return $this->belongsTo(\App\Model\CustomerComplain::class, 'customer_complain_id', 'id');
    }

    public function CustomerComplainStatus()
    {
        return $this->belongsTo(\App\Model\CustomerComplainStatus::class, 'customer_complain_status_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }
}
