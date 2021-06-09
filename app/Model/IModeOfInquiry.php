<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class IModeOfInquiry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_i_mode_of_inquiry';
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
        return $this->hasMany('App\Model\Inquiry', 'mode_of_inquiry_id', 'id')->where('is_delete', 0);
    }

}