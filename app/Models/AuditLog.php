<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'reason',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Foundation-stage helper only. Financial/destructive actions (credit
     * adjustments, suspensions) will call this from their own services in a
     * later stage and MUST pass a reason — that requirement is enforced there,
     * not here, since this model has no opinion on which actions are
     * "financial." Do not relax this once that stage adds real calls.
     */
    public static function record(string $action, array $attributes = []): self
    {
        return static::create(array_merge([
            'organization_id' => auth()->user()?->organization_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'ip_address' => request()?->ip(),
        ], $attributes));
    }
}
