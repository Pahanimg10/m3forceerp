<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DocumentUpload extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'document_upload';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inquiry_id',
        'document_type_id',
        'document_name',
        'upload_document',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Inquiry()
    {
        return $this->belongsTo(\App\Model\Inquiry::class, 'inquiry_id', 'id');
    }

    public function DocumentType()
    {
        return $this->belongsTo(\App\Model\DocumentType::class, 'document_type_id', 'id');
    }
}
