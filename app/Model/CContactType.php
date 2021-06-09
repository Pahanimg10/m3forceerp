<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CContactType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_c_contact_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code', 
        'name', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Contact()
    {
        return $this->hasMany('App\Model\Contact', 'contact_type_id', 'id')->where('is_delete', 0);
    }

}