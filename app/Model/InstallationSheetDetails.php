<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InstallationSheetDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'installation_sheet_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'installation_sheet_id',
        'item_id',
        'rate',
        'quantity',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function InstallationSheet()
    {
        return $this->belongsTo('App\Model\InstallationSheet', 'installation_sheet_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo('App\Model\Item', 'item_id', 'id');
    }

    public function GoodRequestDetails()
    {
        return $this->hasMany('App\Model\GoodRequestDetails', 'detail_id', 'id')->where('is_delete', 0);
    }
}
