<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_region';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->hasMany('App\Model\Contact', 'region_id', 'id')->where('is_delete', 0);
    }

    public function CGroup()
    {
        return $this->hasMany('App\Model\CGroup', 'region_id', 'id')->where('is_delete', 0);
    }
}
