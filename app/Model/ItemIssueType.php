<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemIssueType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'item_issue_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
    ];

    /**
     * Relations
     */
    public function JobAttendance()
    {
        return $this->hasMany(\App\Model\JobAttendance::class, 'job_type_id', 'id')->where('is_delete', 0);
    }

    public function ItemIssue()
    {
        return $this->hasMany(\App\Model\ItemIssue::class, 'item_issue_type_id', 'id')->where('is_delete', 0);
    }

    public function PettyCashIssue()
    {
        return $this->hasMany(\App\Model\PettyCashIssue::class, 'petty_cash_issue_type_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany(\App\Model\Repair::class, 'repair_type_id', 'id')->where('is_delete', 0);
    }
}
