<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseInstallationSheetDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_installation_sheet_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_installation_sheet_id',
        'item_id',
        'rate',
        'quantity',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponseInstallationSheet()
    {
        return $this->belongsTo('App\Model\TechResponseInstallationSheet', 'tech_response_installation_sheet_id', 'id');
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
