<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SideMenu extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'side_menu';
    protected $primaryKey = 'id';
    protected $fillable = [
        'menu_order',
        'menu_category',
        'menu_name',
        'menu_id',
        'menu_icon',
        'menu_url',
    ];

    /**
     * Relations
     */
    public function UserAccess()
    {
        return $this->hasMany(\App\Model\UserAccess::class, 'side_menu_id', 'id');
    }
}
