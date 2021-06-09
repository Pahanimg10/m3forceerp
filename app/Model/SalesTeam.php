<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SalesTeam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_sales_team';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'code',
        'name',
        'contact_no',
        'sales_target',
        'is_active',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Inquiry()
    {
        return $this->hasMany(\App\Model\Inquiry::class, 'sales_team_id', 'id')->where('is_delete', 0);
    }

    public function InquiryDetials()
    {
        return $this->hasMany(\App\Model\InquiryDetials::class, 'sales_team_id', 'id')->where('is_delete', 0);
    }
}
