<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CTaxType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_c_tax_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'percentage',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function ContactTax()
    {
        return $this->hasMany(\App\Model\ContactTax::class, 'tax_id', 'id')->where('is_delete', 0);
    }

    public function CGroupTax()
    {
        return $this->hasMany(\App\Model\CGroupTax::class, 'tax_id', 'id')->where('is_delete', 0);
    }
}
