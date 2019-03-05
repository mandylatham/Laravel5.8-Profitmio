<?php

namespace App\Models\Impersonation;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ImpersonatedAction extends Model
{
    const TYPE_CREATE = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    /**
     * Prevent the use of the `updated_at` field
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'impersonator_id',
        'action',
        'object_type',
        'object_id',
    ];

    /**
     * Polymorphic object relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function object(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Impersonator relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function impersonator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'impersonator_id');
    }

    /**
     * TODO: Remove this method and use API Resources for serialization
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'created_at' => $this->created_at->timestamp,
            'impersonator' => [
                'id' => $this->impersonator->getKey(),
                'first_name' => $this->impersonator->first_name,
                'last_name' => $this->impersonator->last_name,
                'name' => $this->impersonator->name,
            ],
        ];
    }
}
