<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'inquiry';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'inquiry_no',
        'inquiry_date_time',
        'mode_of_inquiry_id',
        'contact_of',
        'inquiry_type_id',
        'sales_team_id',
        'remarks',
        'is_first_call_done',
        'is_completed',
        'user_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->belongsTo(\App\Model\Contact::class, 'contact_id', 'id');
    }

    public function IModeOfInquiry()
    {
        return $this->belongsTo(\App\Model\IModeOfInquiry::class, 'mode_of_inquiry_id', 'id');
    }

    public function IInquiryType()
    {
        return $this->belongsTo(\App\Model\IInquiryType::class, 'inquiry_type_id', 'id');
    }

    public function SalesTeam()
    {
        return $this->belongsTo(\App\Model\SalesTeam::class, 'sales_team_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }

    public function InquiryDetials()
    {
        return $this->hasMany(\App\Model\InquiryDetials::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function DocumentUpload()
    {
        return $this->hasMany(\App\Model\DocumentUpload::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function Job()
    {
        return $this->hasOne(\App\Model\Job::class, 'id', 'inquiry_id');
    }

    public function JobCard()
    {
        return $this->hasMany(\App\Model\JobCard::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function CostSheet()
    {
        return $this->hasMany(\App\Model\CostSheet::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function Quotation()
    {
        return $this->hasMany(\App\Model\Quotation::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function InstallationSheet()
    {
        return $this->hasMany(\App\Model\InstallationSheet::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function QuotationCostSheet()
    {
        return $this->hasMany(\App\Model\QuotationCostSheet::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function CompletedJobs()
    {
        return $this->hasMany(\App\Model\CompletedJobs::class, 'inquiry_id', 'id')->where('is_delete', 0);
    }
}
