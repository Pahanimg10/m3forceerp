<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'rate';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'value'
    ];

    /**
     * Relations
     */

}