<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ComplainType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_complain_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function CustomerComplain()
    {
        return $this->hasMany(\App\Model\CustomerComplain::class, 'complain_type_id', 'id')->where('is_delete', 0);
    }
}
