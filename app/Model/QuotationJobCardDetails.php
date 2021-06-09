<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuotationJobCardDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'quotation_job_card_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quotation_job_card_id', 
        'item_id', 
        'rate',  
        'quantity', 
        'margin', 
        'is_main', 
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function QuotationJobCard()
    {
        return $this->belongsTo('App\Model\QuotationJobCard', 'quotation_job_card_id', 'id');
    }
    
    public function Item()
    {
        return $this->belongsTo('App\Model\Item', 'item_id', 'id');
    }

}