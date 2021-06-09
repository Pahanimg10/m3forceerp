<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_item';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contact_id',
        'main_category_id',
        'sub_category_id',
        'purchase_type_id',
        'code',
        'name',
        'model_no',
        'brand',
        'origin',
        'unit_type_id',
        'reorder_level',
        'rate',
        'stock',
        'is_serial',
        'is_warranty',
        'is_active',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Contact()
    {
        return $this->belongsTo('App\Model\Contact', 'contact_id', 'id');
    }

    public function MainItemCategory()
    {
        return $this->belongsTo('App\Model\MainItemCategory', 'main_category_id', 'id');
    }

    public function SubItemCategory()
    {
        return $this->belongsTo('App\Model\SubItemCategory', 'sub_category_id', 'id');
    }

    public function PurchaseType()
    {
        return $this->belongsTo('App\Model\PurchaseType', 'purchase_type_id', 'id');
    }

    public function UnitType()
    {
        return $this->belongsTo('App\Model\UnitType', 'unit_type_id', 'id');
    }

    public function JobCardDetails()
    {
        return $this->hasMany('App\Model\JobCardDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function InstallationSheetDetails()
    {
        return $this->hasMany('App\Model\InstallationSheetDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function GoodRequestDetails()
    {
        return $this->hasMany('App\Model\GoodRequestDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function PurchaseOrderDetails()
    {
        return $this->hasMany('App\Model\PurchaseOrderDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function GoodReceiveDetails()
    {
        return $this->hasMany('App\Model\GoodReceiveDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function ItemIssueDetails()
    {
        return $this->hasMany('App\Model\ItemIssueDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function ItemReceiveDetails()
    {
        return $this->hasMany('App\Model\ItemReceiveDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseJobCardDetails()
    {
        return $this->hasMany('App\Model\TechResponseJobCardDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseInstallationSheetDetails()
    {
        return $this->hasMany('App\Model\TechResponseInstallationSheetDetails', 'item_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany('App\Model\Repair', 'item_id', 'id')->where('is_delete', 0);
    }

    public function QuotationJobCardDetails()
    {
        return $this->hasMany('App\Model\QuotationJobCardDetails', 'item_id', 'id')->where('is_delete', 0);
    }
}
