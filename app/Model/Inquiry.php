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
        return $this->belongsTo('App\Model\Contact', 'contact_id', 'id');
    }

    public function IModeOfInquiry()
    {
        return $this->belongsTo('App\Model\IModeOfInquiry', 'mode_of_inquiry_id', 'id');
    }

    public function IInquiryType()
    {
        return $this->belongsTo('App\Model\IInquiryType', 'inquiry_type_id', 'id');
    }

    public function SalesTeam()
    {
        return $this->belongsTo('App\Model\SalesTeam', 'sales_team_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

    public function InquiryDetials()
    {
        return $this->hasMany('App\Model\InquiryDetials', 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function DocumentUpload()
    {
        return $this->hasMany('App\Model\DocumentUpload', 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function Job()
    {
        return $this->hasOne('App\Model\Job', 'id', 'inquiry_id');
    }

    public function JobCard()
    {
        return $this->hasMany('App\Model\JobCard', 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function CostSheet()
    {
        return $this->hasMany('App\Model\CostSheet', 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function Quotation()
    {
        return $this->hasMany('App\Model\Quotation', 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function InstallationSheet()
    {
        return $this->hasMany('App\Model\InstallationSheet', 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function QuotationCostSheet()
    {
        return $this->hasMany('App\Model\QuotationCostSheet', 'inquiry_id', 'id')->where('is_delete', 0);
    }

    public function CompletedJobs()
    {
        return $this->hasMany('App\Model\CompletedJobs', 'inquiry_id', 'id')->where('is_delete', 0);
    }
}
