<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CostSheet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'cost_sheet';
    protected $primaryKey = 'id';
    protected $fillable = [
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
        'is_used',
        'user_id',
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Inquiry()
    {
        return $this->belongsTo('App\Model\Inquiry', 'inquiry_id', 'id');
    }
    
    public function InstallationRate()
    {
        return $this->belongsTo('App\Model\InstallationRate', 'installation_rate_id', 'id');
    }
    
    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
    
    public function QuotationCostSheet()
    {
        return $this->hasMany('App\Model\QuotationCostSheet', 'cost_sheet_id', 'id')->where('is_delete', 0);
    }

}