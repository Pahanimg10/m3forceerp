<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_c_group';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
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
        'invoice_name',
        'invoice_delivering_address',
        'collection_address',
        'invoice_email',
        'vat_no',
        'svat_no',
        'monitoring_fee',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Region()
    {
        return $this->belongsTo(\App\Model\Region::class, 'region_id', 'id');
    }

    public function CollectionManager()
    {
        return $this->belongsTo(\App\Model\CollectionManager::class, 'collection_manager_id', 'id');
    }

    public function Contact()
    {
        return $this->hasMany(\App\Model\Contact::class, 'group_id', 'id')->where('is_delete', 0);
    }

    public function CGroupTax()
    {
        return $this->hasMany(\App\Model\CGroupTax::class, 'group_id', 'id')->where('is_delete', 0);
    }

    public function CGroupInvoiceMonth()
    {
        return $this->hasMany(\App\Model\CGroupInvoiceMonth::class, 'group_id', 'id')->where('is_delete', 0);
    }

    public function MonitoringCustomer()
    {
        return $this->hasMany(\App\Model\MonitoringCustomer::class, 'contact_id', 'id')->where('is_delete', 0);
    }
}
