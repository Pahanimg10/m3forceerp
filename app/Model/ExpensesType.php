<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ExpensesType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'expenses_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
    ];

    /**
     * Relations
     */
    public function ActualExpenses()
    {
        return $this->hasMany('App\Model\ActualExpenses', 'expenses_type_id', 'id')->where('is_delete', 0);
    }
}
