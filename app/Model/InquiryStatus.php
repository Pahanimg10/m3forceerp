<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InquiryStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'inquiry_status';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'show_update',
    ];

    /**
     * Relations
     */
    public function InquiryDetials()
    {
        return $this->hasMany(\App\Model\InquiryDetials::class, 'inquiry_status_id', 'id')->where('is_delete', 0);
    }
}
