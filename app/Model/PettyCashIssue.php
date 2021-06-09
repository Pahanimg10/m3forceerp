<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PettyCashIssue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'petty_cash_issue';
    protected $primaryKey = 'id';
    protected $fillable = [
        'petty_cash_issue_type_id',
        'document_id',
        'petty_cash_issue_no',
        'petty_cash_request_date_time',
        'issued_to',
        'issue_mode_id',
        'petty_cash_issue_value',
        'cheque_no',
        'bank',
        'remarks',
        'is_posted',
        'is_delete',
        'logged_user',
        'posted_user',
        'petty_cash_issue_date_time',
    ];

    /**
     * Relations
     */
    public function ItemIssueType()
    {
        return $this->belongsTo('App\Model\ItemIssueType', 'petty_cash_issue_type_id', 'id');
    }

    public function Job()
    {
        return $this->belongsTo('App\Model\Job', 'document_id', 'id');
    }

    public function TechResponse()
    {
        return $this->belongsTo('App\Model\TechResponse', 'document_id', 'id');
    }

    public function IssueMode()
    {
        return $this->belongsTo('App\Model\PaymentMode', 'issue_mode_id', 'id');
    }

    public function PettyCashReturn()
    {
        return $this->hasMany('App\Model\PettyCashReturn', 'petty_cash_issue_id', 'id')->where('is_delete', 0);
    }

    public function LoggedUser()
    {
        return $this->belongsTo('App\Model\User', 'logged_user', 'id');
    }

    public function PostedUser()
    {
        return $this->belongsTo('App\Model\User', 'posted_user', 'id');
    }
}
