<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_company';
    protected $primaryKey = 'id';
    protected $fillable = [
        'company_name', 
        'phone_number',  
        'hotline_number', 
        'email', 
        'website', 
        'address_line_1', 
        'address_line_2', 
        'address_line_3', 
        'reg_number', 
        'svat',
        'vat',
        'company_image'
    ];

    /**
     * Relations
     */
}