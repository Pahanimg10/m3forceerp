<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CollectionPerson extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_collection_person';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'contact_no',
        'is_active',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function JobDoneCustomerPayment()
    {
        return $this->hasMany(\App\Model\JobDoneCustomerPayment::class, 'collection_person_id', 'id')->where('is_delete', 0);
    }

    public function MonitoringCustomerPayment()
    {
        return $this->hasMany(\App\Model\MonitoringCustomerPayment::class, 'collection_person_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseCustomerPayment()
    {
        return $this->hasMany(\App\Model\TechResponseCustomerPayment::class, 'collection_person_id', 'id')->where('is_delete', 0);
    }
}
