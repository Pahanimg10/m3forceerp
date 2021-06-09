<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'tech_response_fault_id',
        'tech_response_no',
        'record_date_time',
        'remarks',
        'reported_person',
        'reported_contact_no',
        'reported_email',
        'tech_response_value',
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

    public function TechResponseFault()
    {
        return $this->belongsTo('App\Model\TechResponseFault', 'tech_response_fault_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

    public function TechResponseDetails()
    {
        return $this->hasMany('App\Model\TechResponseDetails', 'tech_response_id', 'id')->where('is_delete', 0);
    }

    public function JobAttendance()
    {
        return $this->hasMany('App\Model\JobAttendance', 'job_id', 'id')->where('is_delete', 0);
    }

    public function ItemIssue()
    {
        return $this->hasMany('App\Model\ItemIssue', 'document_id', 'id')->where('is_delete', 0);
    }

    public function PettyCashIssue()
    {
        return $this->hasMany('App\Model\PettyCashIssue', 'document_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseJobCard()
    {
        return $this->hasMany('App\Model\TechResponseJobCard', 'tech_response_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseInstallationSheet()
    {
        return $this->hasMany('App\Model\TechResponseInstallationSheet', 'tech_response_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseQuotation()
    {
        return $this->hasMany('App\Model\TechResponseQuotation', 'tech_response_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany('App\Model\Repair', 'document_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseInvoiceDetails()
    {
        return $this->hasMany('App\Model\TechResponseInvoiceDetails', 'tech_response_id', 'id')->where('is_delete', 0);
    }
}
