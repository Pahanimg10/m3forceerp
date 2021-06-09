<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoodRequestDocument extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'good_request_documents';
    protected $primaryKey = 'id';
    protected $fillable = [
        'good_request_id',
        'type',
        'document_id',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function GoodRequest()
    {
        return $this->belongsTo('App\Model\GoodRequest', 'good_request_id', 'id');
    }

    public function JobCard()
    {
        return $this->belongsTo('App\Model\JobCard', 'document_id', 'id');
    }

    public function InstallationSheet()
    {
        return $this->belongsTo('App\Model\InstallationSheet', 'document_id', 'id');
    }

    public function TechResponseJobCard()
    {
        return $this->belongsTo('App\Model\TechResponseJobCard', 'document_id', 'id');
    }

    public function TechResponseInstallationSheet()
    {
        return $this->belongsTo('App\Model\TechResponseInstallationSheet', 'document_id', 'id');
    }
}
