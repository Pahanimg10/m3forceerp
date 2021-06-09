<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InstallationRate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'installation_rate';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'installation_cost',
        'labour',
        'rate',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function CostSheet()
    {
        return $this->hasMany(\App\Model\CostSheet::class, 'installation_rate_id', 'id')->where('is_delete', 0);
    }

    public function QuotationCostSheet()
    {
        return $this->hasMany(\App\Model\QuotationCostSheet::class, 'installation_rate_id', 'id')->where('is_delete', 0);
    }
}
