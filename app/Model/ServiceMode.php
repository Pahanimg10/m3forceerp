<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ServiceMode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'service_mode';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->hasMany(\App\Model\Contact::class, 'service_mode_id', 'id')->where('is_delete', 0);
    }
}
