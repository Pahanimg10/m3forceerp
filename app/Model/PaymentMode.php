<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'payment_mode';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
    ];

    /**
     * Relations
     */
    public function InquiryDetials()
    {
        return $this->hasMany('App\Model\InquiryDetials', 'payment_mode_id', 'id')->where('is_delete', 0);
    }

    public function CreditSupplierPayment()
    {
        return $this->hasMany('App\Model\CreditSupplierPayment', 'payment_mode_id', 'id')->where('is_delete', 0);
    }

    public function JobDoneCustomerPayment()
    {
        return $this->hasMany('App\Model\JobDoneCustomerPayment', 'payment_mode_id', 'id')->where('is_delete', 0);
    }

    public function MonitoringCustomerPayment()
    {
        return $this->hasMany('App\Model\MonitoringCustomerPayment', 'payment_mode_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseCustomerPayment()
    {
        return $this->hasMany('App\Model\TechResponseCustomerPayment', 'payment_mode_id', 'id')->where('is_delete', 0);
    }

    public function PettyCashIssue()
    {
        return $this->hasMany('App\Model\PettyCashIssue', 'issue_mode_id', 'id')->where('is_delete', 0);
    }
}
