<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoodRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'good_request';
    protected $primaryKey = 'id';
    protected $fillable = [
        'good_request_no',
        'good_request_date_time',
        'remarks',
        'good_request_value',
        'is_posted',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function GoodRequestDetails()
    {
        return $this->hasMany(\App\Model\GoodRequestDetails::class, 'good_request_id', 'id')->where('is_delete', 0);
    }

    public function GoodRequestDocument()
    {
        return $this->hasMany(\App\Model\GoodRequestDocument::class, 'good_request_id', 'id')->where('is_delete', 0);
    }

    public function GoodRequest()
    {
        return $this->hasMany(self::class, 'good_request_id', 'id')->where('is_delete', 0);
    }
}
