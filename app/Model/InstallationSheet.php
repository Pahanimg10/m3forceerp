<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InstallationSheet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;
    protected $table = 'installation_sheet';
    protected $primaryKey = 'id';
    protected $fillable = [
        'inquiry_id',
        'installation_sheet_no',
        'installation_sheet_date_time',
        'remarks',
        'installation_sheet_value',
        'user_id',
        'is_posted',
        'is_approved',
        'is_ordered',
        'is_delete',
    ];

    /**
     * Relations
     */
    public function Inquiry()
    {
        return $this->belongsTo('App\Model\Inquiry', 'inquiry_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }

    public function InstallationSheetDetails()
    {
        return $this->hasMany('App\Model\InstallationSheetDetails', 'installation_sheet_id', 'id')->where('is_delete', 0);
    }

    public function GoodRequestDocument()
    {
        return $this->hasMany('App\Model\GoodRequestDocument', 'document_id', 'id')->where('is_delete', 0);
    }
}
