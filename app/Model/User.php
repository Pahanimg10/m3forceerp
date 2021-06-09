<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

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
        return $this->belongsTo(\App\Model\JobPosition::class, 'job_position_id', 'id');
    }

    public function UserGroupPermission()
    {
        return $this->hasMany(\App\Model\UserGroupPermission::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function Inquiry()
    {
        return $this->hasMany(\App\Model\Inquiry::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function InquiryDetials()
    {
        return $this->hasMany(\App\Model\InquiryDetials::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function JobDetails()
    {
        return $this->hasMany(\App\Model\JobDetails::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function JobCard()
    {
        return $this->hasMany(\App\Model\JobCard::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function CostSheet()
    {
        return $this->hasMany(\App\Model\CostSheet::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function Quotation()
    {
        return $this->hasMany(\App\Model\Quotation::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function InstallationSheet()
    {
        return $this->hasMany(\App\Model\InstallationSheet::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponse()
    {
        return $this->hasMany(\App\Model\TechResponse::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseDetails()
    {
        return $this->hasMany(\App\Model\TechResponseDetails::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseJobCard()
    {
        return $this->hasMany(\App\Model\TechResponseJobCard::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseInstallationSheet()
    {
        return $this->hasMany(\App\Model\TechResponseInstallationSheet::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseQuotation()
    {
        return $this->hasMany(\App\Model\TechResponseQuotation::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany(\App\Model\Repair::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function RepairDetails()
    {
        return $this->hasMany(\App\Model\RepairDetails::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function QuotationCostSheet()
    {
        return $this->hasMany(\App\Model\QuotationCostSheet::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function CustomerComplain()
    {
        return $this->hasMany(\App\Model\CustomerComplain::class, 'user_id', 'id')->where('is_delete', 0);
    }

    public function CustomerComplainDetails()
    {
        return $this->hasMany(\App\Model\CustomerComplainDetails::class, 'user_id', 'id')->where('is_delete', 0);
    }
}
