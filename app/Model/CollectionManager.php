<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CollectionManager extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_collection_manager';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'contact_no',
        'is_active',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function CGroup()
    {
        return $this->hasMany('App\Model\CGroup', 'collection_manager_id', 'id')->where('is_delete', 0);
    }

    public function Contact()
    {
        return $this->hasMany('App\Model\Contact', 'collection_manager_id', 'id')->where('is_delete', 0);
    }
}
