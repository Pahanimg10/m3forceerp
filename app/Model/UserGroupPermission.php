<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'user_group_permission';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'user_group_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

    public function UserGroup()
    {
        return $this->belongsTo('App\Model\UserGroup', 'user_group_id', 'id');
    }
}
