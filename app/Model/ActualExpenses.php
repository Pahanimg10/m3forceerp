<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActualExpenses extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'actual_expenses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'record_id', 
        'expenses_date_time',
        'expenses_type_id',
        'expenses_value',
        'excavation_work', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function Inquiry()
    {
        return $this->belongsTo('App\Model\Inquiry', 'record_id', 'id');
    }
    
    public function ExpensesType()
    {
        return $this->belongsTo('App\Model\ExpensesType', 'expenses_type_id', 'id');
    }

}