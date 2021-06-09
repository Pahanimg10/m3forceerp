<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerComplain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'customer_complain';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'complain_type_id',
        'person_responsible_id',
        'complain_no',
        'record_date_time',
        'remarks',
        'reported_person',
        'reported_contact_no',
        'reported_email',
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

    public function ComplainType()
    {
        return $this->belongsTo(\App\Model\ComplainType::class, 'complain_type_id', 'id');
    }

    public function PersonResponsible()
    {
        return $this->belongsTo(\App\Model\PersonResponsible::class, 'person_responsible_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }

    public function CustomerComplainDetails()
    {
        return $this->hasMany(\App\Model\CustomerComplainDetails::class, 'customer_complain_id', 'id')->where('is_delete', 0);
    }
}
