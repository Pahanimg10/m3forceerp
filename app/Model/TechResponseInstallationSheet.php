<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TechResponseInstallationSheet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'tech_response_installation_sheet';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tech_response_id',
        'tech_response_installation_sheet_no',
        'tech_response_installation_sheet_date_time',
        'remarks',
        'tech_response_installation_sheet_value',
        'user_id',
        'is_ordered',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function TechResponse()
    {
        return $this->belongsTo(\App\Model\TechResponse::class, 'tech_response_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(\App\Model\User::class, 'user_id', 'id');
    }

    public function TechResponseInstallationSheetDetails()
    {
        return $this->hasMany(\App\Model\TechResponseInstallationSheetDetails::class, 'tech_response_installation_sheet_id', 'id')->where('is_delete', 0);
    }

    public function GoodRequestDocument()
    {
        return $this->hasMany(\App\Model\GoodRequestDocument::class, 'document_id', 'id')->where('is_delete', 0);
    }
}
