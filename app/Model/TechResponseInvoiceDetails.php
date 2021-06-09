<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseInvoiceDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_invoice_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_id', 
        'item_id', 
        'rate', 
        'quantity',
        'value',  
        'invoice_value',  
        'is_delete'
    ];

    /**
     * Relations
     */
    
    public function TechResponse()
    {
        return $this->belongsTo('App\Model\TechResponse', 'tech_response_id', 'id');
    }
    
    public function Item()
    {
        return $this->belongsTo('App\Model\Item', 'item_id', 'id');
    }

}