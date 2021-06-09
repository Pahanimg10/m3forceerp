<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuotationCostSheet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'quotation_cost_sheet';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quotation_id',
        'cost_sheet_id',
        'inquiry_id',
        'cost_sheet_no',
        'cost_sheet_date_time',
        'installation_rate_id',
        'meters',
        'excavation_work',
        'transport',
        'food',
        'accommodation',
        'bata',
        'other_expenses',
        'remarks',
        'cost_sheet_value',
        'installation_value',
        'mandays',
        'user_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Quotation()
    {
        return $this->belongsTo(\App\Model\Quotation::class, 'quotation_id', 'id');
    }

    public function CostSheet()
    {
        return $this->belongsTo(\App\Model\CostSheet::class, 'cost_sheet_id', 'id');
    }

    public function Inquiry()
    {
        return $this->belongsTo(\App\Model\Inquiry::class, 'inquiry_id', 'id');
    }

    public function InstallationRate()
    {
        return $this->belongsTo(\App\Model\InstallationRate::class, 'installation_rate_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }
}
