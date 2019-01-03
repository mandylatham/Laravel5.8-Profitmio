<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignScheduleTemplate extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";

    protected $fillable = [
        'name', 'type', 'email_subject', 'email_text', 'email_html',
        'text_message', 'text_vehicle_image', 'send_vehicle_image'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeFilterByCompany($query, Company $company)
    {
        session(['filters.template.index.company' => $company->id]);
        return $query->where(function ($query) use ($company) {
            $query->orWhere('agency_id', $company->id);
            $query->orWhere('dealership_id', $company->id);
        });
    }

    public function scopeFilterByQuery($query, $q)
    {
        session(['filters.template.index.q' => $q]);
        return $query->search($q);
    }

    public static function searchByRequest(Request $request)
    {
        $query = self::query()
            ->whereNull('deleted_at');

        if ($request->has('company')) {
            $query->filterByCompany(Company::findOrFail($request->input('company')));
        } else {
            session()->forget('filters.template.index.company');
        }
        if ($request->has('q')) {
            $query->filterByQuery($request->input('q'));
        } else {
            session()->forget('filters.template.index.q');
        }
        return $query;
    }
}
