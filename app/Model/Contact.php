<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_contact';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_type_id', 
        'business_type_id', 
        'contact_id', 
        'code', 
        'name', 
        'nic', 
        'address', 
        'contact_no', 
        'email',  
        'region_id', 
        'collection_manager_id', 
        'contact_person_1', 
        'contact_person_no_1', 
        'contact_person_2', 
        'contact_person_no_2',
        'contact_person_3', 
        'contact_person_no_3',
        'start_date', 
        'end_date', 
        'invoice_name', 
        'invoice_delivering_address',
        'collection_address', 
        'invoice_email',
        'vat_no', 
        'svat_no',
        'monitoring_fee',
        'service_mode_id',
        'client_type_id',
        'group_id',
        'is_group',
        'is_active',  
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function CContactType()
    {
        return $this->belongsTo('App\Model\CContactType', 'contact_type_id', 'id');
    }
    
    public function IBusinessType()
    {
        return $this->belongsTo('App\Model\IBusinessType', 'business_type_id', 'id');
    }
    
    public function Region()
    {
        return $this->belongsTo('App\Model\Region', 'region_id', 'id');
    }
    
    public function CollectionManager()
    {
        return $this->belongsTo('App\Model\CollectionManager', 'collection_manager_id', 'id');
    }
    
    public function CGroup()
    {
        return $this->belongsTo('App\Model\CGroup', 'group_id', 'id');
    }
    
    public function ServiceMode()
    {
        return $this->belongsTo('App\Model\ServiceMode', 'service_mode_id', 'id');
    }
    
    public function IClientType()
    {
        return $this->belongsTo('App\Model\IClientType', 'client_type_id', 'id');
    }
    
    public function ContactTax()
    {
        return $this->hasMany('App\Model\ContactTax', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function ContactInvoiceMonth()
    {
        return $this->hasMany('App\Model\ContactInvoiceMonth', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function Inquiry()
    {
        return $this->hasMany('App\Model\Inquiry', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function Item()
    {
        return $this->hasMany('App\Model\PurchaseOrder', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function PurchaseOrder()
    {
        return $this->hasMany('App\Model\PurchaseOrder', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function CreditSupplier()
    {
        return $this->hasMany('App\Model\CreditSupplier', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function JobDoneCustomer()
    {
        return $this->hasMany('App\Model\JobDoneCustomer', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function MonitoringCustomer()
    {
        return $this->hasMany('App\Model\MonitoringCustomer', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function TechResponse()
    {
        return $this->hasMany('App\Model\TechResponse', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function TechResponseCustomer()
    {
        return $this->hasMany('App\Model\TechResponseCustomer', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function Repair()
    {
        return $this->hasMany('App\Model\Repair', 'contact_id', 'id')->where('is_delete', 0);
    }
    
    public function CustomerComplain()
    {
        return $this->hasMany('App\Model\CustomerComplain', 'contact_id', 'id')->where('is_delete', 0);
    }

}