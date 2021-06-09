<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'first_name',
        'last_name',
        'contact_no',
        'email',
        'job_position_id',
        'user_image',
        'username',
        'password',
        'is_delete',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Relations
     */
    public function JobPosition()
    {
        return $this->belongsTo('App\Model\JobPosition', 'job_position_id', 'id');
    }

    public function UserGroupPermission()
    {
        return $this->hasMany('App\Model\UserGroupPermission', 'user_id', 'id')->where('is_delete', 0);
    }

    public function Inquiry()
    {
        return $this->hasMany('App\Model\Inquiry', 'user_id', 'id')->where('is_delete', 0);
    }

    public function InquiryDetials()
    {
        return $this->hasMany('App\Model\InquiryDetials', 'user_id', 'id')->where('is_delete', 0);
    }

    public function JobDetails()
    {
        return $this->hasMany('App\Model\JobDetails', 'user_id', 'id')->where('is_delete', 0);
    }

    public function JobCard()
    {
        return $this->hasMany('App\Model\JobCard', 'user_id', 'id')->where('is_delete', 0);
    }

    public function CostSheet()
    {
        return $this->hasMany('App\Model\CostSheet', 'user_id', 'id')->where('is_delete', 0);
    }

    public function Quotation()
    {
        return $this->hasMany('App\Model\Quotation', 'user_id', 'id')->where('is_delete', 0);
    }

    public function InstallationSheet()
    {
        return $this->hasMany('App\Model\InstallationSheet', 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponse()
    {
        return $this->hasMany('App\Model\TechResponse', 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseDetails()
    {
        return $this->hasMany('App\Model\TechResponseDetails', 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseJobCard()
    {
        return $this->hasMany('App\Model\TechResponseJobCard', 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseInstallationSheet()
    {
        return $this->hasMany('App\Model\TechResponseInstallationSheet', 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseQuotation()
    {
        return $this->hasMany('App\Model\TechResponseQuotation', 'user_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany('App\Model\Repair', 'user_id', 'id')->where('is_delete', 0);
    }

    public function RepairDetails()
    {
        return $this->hasMany('App\Model\RepairDetails', 'user_id', 'id')->where('is_delete', 0);
    }

    public function QuotationCostSheet()
    {
        return $this->hasMany('App\Model\QuotationCostSheet', 'user_id', 'id')->where('is_delete', 0);
    }

    public function CustomerComplain()
    {
        return $this->hasMany('App\Model\CustomerComplain', 'user_id', 'id')->where('is_delete', 0);
    }

    public function CustomerComplainDetails()
    {
        return $this->hasMany('App\Model\CustomerComplainDetails', 'user_id', 'id')->where('is_delete', 0);
    }
}
