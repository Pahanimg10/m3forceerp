<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'user_access';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_group_id',
        'side_menu_id',
    ];

    /**
     * Relations
     */
    public function UserGroup()
    {
        return $this->belongsTo('App\Model\UserGroup', 'user_group_id', 'id');
    }

    public function SideMenu()
    {
        return $this->belongsTo('App\Model\SideMenu', 'side_menu_id', 'id');
    }
}
