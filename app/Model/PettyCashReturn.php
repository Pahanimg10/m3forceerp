<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PettyCashReturn extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'petty_cash_return';
    protected $primaryKey = 'id';
    protected $fillable = [ 
        'petty_cash_issue_id',
        'petty_cash_return_no', 
        'petty_cash_return_date_time',
        'petty_cash_return_value', 
        'remarks', 
        'is_posted',
        'is_delete',
        'logged_user',
        'posted_user'
    ];

    /**
     * Relations
     */
    
    public function PettyCashIssue()
    {
        return $this->belongsTo('App\Model\PettyCashIssue', 'petty_cash_issue_id', 'id');
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