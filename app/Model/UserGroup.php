<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'user_group';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'permission',
    ];

    /**
     * Relations
     */
    public function UserGroupPermission()
    {
        return $this->hasMany(\App\Model\UserGroupPermission::class, 'user_group_id', 'id')->where('is_delete', 0);
    }
}
