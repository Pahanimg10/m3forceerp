<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class IInquiryType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_i_inquiry_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code', 
        'name', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Inquiry()
    {
        return $this->hasMany('App\Model\Inquiry', 'inquiry_type_id', 'id')->where('is_delete', 0);
    }

}