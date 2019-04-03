<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class RecipientList extends Model implements HasMedia
{
    use HasMediaTrait, SoftDeletes;

    protected $table = 'recipient_lists';

    protected $fillable = [
        'campaign_id', 'uploaded_by', 'name', 'type', 'email_validated', 'phones_validated', 'total_valid_emails',
        'total_valid_phones', 'total_conquest', 'total_dealer_db', 'total_recipients', 'upload_identifier',
        'recipients_added', 'fieldmap', 'recipients_addeed', 'failed_at', 'failed_reason',
    ];

    protected $casts = [
        'fieldmap' => 'array',
    ];

    protected $dates = ['failed_at'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function recipients()
    {
        return $this->hasMany(Recipient::class);
    }

    public function withEmails()
    {
        return $this->recipients()->where('email', '<>', '')->count();
    }

    public function withPhones()
    {
        return $this->recipients()->where('phone', '<>', '')->count();
    }

    public function fromDealerDb()
    {
        return $this->recipients()->whereFromDealerDb(true)->count();
    }

    public function fromConquest()
    {
        return $this->recipients()->whereFromDealerDb(false)->count();
    }

    public function inDrops()
    {
        return DB::select("select count(*) as count from deployment_targets where target_id in (
          select target_id from targets where recipient_list_id = {$this->id})")[0]->count;
    }

    public function inDeployedDrops()
    {
        return DB::select("select count(*) as count from deployment_targets where target_id in (
          select target_id from targets where recipient_list_id = {$this->id}) and sent_at is not null")[0]->count;
    }
}
