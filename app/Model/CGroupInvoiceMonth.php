<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CGroupInvoiceMonth extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_c_group_inv_month';
    protected $primaryKey = 'id';
    protected $fillable = [
        'group_id',
        'month',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function CGroup()
    {
        return $this->belongsTo('App\Model\CGroup', 'group_id', 'id');
    }
}
