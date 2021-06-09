<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoodReceiveBreakdown extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'good_receive_breakdown';
    protected $primaryKey = 'id';
    protected $fillable = [
        'good_receive_detail_id',
        'serial_no',
        'is_main',
        'main_id',
        'is_issued',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function GoodReceiveDetails()
    {
        return $this->belongsTo(\App\Model\GoodReceiveDetails::class, 'good_receive_detail_id', 'id');
    }

    public function ItemIssueBreakdown()
    {
        return $this->hasMany(\App\Model\ItemIssueBreakdown::class, 'detail_id', 'id')->where('is_delete', 0);
    }

    public function ItemReceiveBreakdown()
    {
        return $this->hasMany(\App\Model\ItemReceiveBreakdown::class, 'detail_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany(\App\Model\Repair::class, 'replace_serial_id', 'id')->where('is_delete', 0);
    }
}
