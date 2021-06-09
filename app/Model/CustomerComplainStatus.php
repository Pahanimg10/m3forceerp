<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerComplainStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'customer_complain_status';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'show_update',
    ];

    /**
     * Relations
     */
    public function CustomerComplainDetails()
    {
        return $this->hasMany(\App\Model\CustomerComplainDetails::class, 'customer_complain_status_id', 'id')->where('is_delete', 0);
    }
}
