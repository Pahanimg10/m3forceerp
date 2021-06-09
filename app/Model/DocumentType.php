<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'm_document_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function DocumentUpload()
    {
        return $this->hasMany('App\Model\DocumentUpload', 'document_type_id', 'id')->where('is_delete', 0);
    }
}
