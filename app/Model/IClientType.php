<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class IClientType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_i_client_type';
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
        return $this->hasMany(\App\Model\Contact::class, 'client_type_id', 'id')->where('is_delete', 0);
    }
}
