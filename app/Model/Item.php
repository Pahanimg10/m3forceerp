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
        return $this->belongsTo(\App\Model\Contact::class, 'contact_id', 'id');
    }

    public function MainItemCategory()
    {
        return $this->belongsTo(\App\Model\MainItemCategory::class, 'main_category_id', 'id');
    }

    public function SubItemCategory()
    {
        return $this->belongsTo(\App\Model\SubItemCategory::class, 'sub_category_id', 'id');
    }

    public function PurchaseType()
    {
        return $this->belongsTo(\App\Model\PurchaseType::class, 'purchase_type_id', 'id');
    }

    public function UnitType()
    {
        return $this->belongsTo(\App\Model\UnitType::class, 'unit_type_id', 'id');
    }

    public function JobCardDetails()
    {
        return $this->hasMany(\App\Model\JobCardDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function InstallationSheetDetails()
    {
        return $this->hasMany(\App\Model\InstallationSheetDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function GoodRequestDetails()
    {
        return $this->hasMany(\App\Model\GoodRequestDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function PurchaseOrderDetails()
    {
        return $this->hasMany(\App\Model\PurchaseOrderDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function GoodReceiveDetails()
    {
        return $this->hasMany(\App\Model\GoodReceiveDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function ItemIssueDetails()
    {
        return $this->hasMany(\App\Model\ItemIssueDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function ItemReceiveDetails()
    {
        return $this->hasMany(\App\Model\ItemReceiveDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseJobCardDetails()
    {
        return $this->hasMany(\App\Model\TechResponseJobCardDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function TechResponseInstallationSheetDetails()
    {
        return $this->hasMany(\App\Model\TechResponseInstallationSheetDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function Repair()
    {
        return $this->hasMany(\App\Model\Repair::class, 'item_id', 'id')->where('is_delete', 0);
    }

    public function QuotationJobCardDetails()
    {
        return $this->hasMany(\App\Model\QuotationJobCardDetails::class, 'item_id', 'id')->where('is_delete', 0);
    }
}
