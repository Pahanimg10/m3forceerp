<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InquiryDetials extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inquiry_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inquiry_id', 
        'update_date_time', 
        'inquiry_status_id', 
        'sales_team_id', 
        'site_inspection_date_time',
        'advance_payment', 
        'payment_mode_id', 
        'receipt_no', 
        'cheque_no', 
        'bank', 
        'realize_date', 
        'remarks',  
        'user_id', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Inquiry()
    {
        return $this->belongsTo('App\Model\Inquiry', 'inquiry_id', 'id');
    }
    
    public function InquiryStatus()
    {
        return $this->belongsTo('App\Model\InquiryStatus', 'inquiry_status_id', 'id');
    }
    
    public function SalesTeam()
    {
        return $this->belongsTo('App\Model\SalesTeam', 'sales_team_id', 'id');
    }
    
    public function PaymentMode()
    {
        return $this->belongsTo('App\Model\PaymentMode', 'payment_mode_id', 'id');
    }
    
    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

}