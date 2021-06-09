<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CompletedJobs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'completed_jobs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inquiry_id',
        'completed_date',
        'invoice_value',
    ];

    /**
     * Relations
     */
    public function Inquiry()
    {
        return $this->belongsTo(\App\Model\Inquiry::class, 'inquiry_id', 'id');
    }
}
