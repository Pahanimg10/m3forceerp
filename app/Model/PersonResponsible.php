<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PersonResponsible extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_person_responsible';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'title',
        'contact_no',
        'email',
        'head_name',
        'head_contact_no',
        'head_email',
        'is_active',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function CustomerComplain()
    {
        return $this->hasMany('App\Model\CustomerComplain', 'person_responsible_id', 'id')->where('is_delete', 0);
    }
}
