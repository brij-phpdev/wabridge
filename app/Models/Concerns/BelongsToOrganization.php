<?php

namespace App\Models\Concerns;

use App\Enums\UserRole;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Every tenant-owned model (Event, Contact, AutomationWorkflow, etc. — added in
 * later foundation stages) uses this trait rather than repeating scope logic
 * per-controller. This is the security boundary referenced throughout the
 * architecture docs: tenant isolation lives here, once, not in every query site.
 *
 * Platform admins (role = platform_admin) are NOT auto-scoped — they only see
 * organization data when explicitly acting in a managed-access context, which
 * is implemented at the service layer in a later stage, not by bypassing this
 * scope silently.
 */
trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $user = Auth::user();

            if (! $user) {
                return;
            }

            if ($user->role === UserRole::PlatformAdmin->value) {
                // Platform admins pass through here unscoped only for their own
                // /admin/* screens. Managed-access "act as this org" mode sets an
                // explicit organization_id on the request in a later stage and
                // must not rely on this branch.
                return;
            }

            $builder->where($builder->getModel()->getTable().'.organization_id', $user->organization_id);
        });

        static::creating(function ($model) {
            if (empty($model->organization_id) && Auth::check()) {
                $model->organization_id = Auth::user()->organization_id;
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
