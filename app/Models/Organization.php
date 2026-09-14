<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'timezone_default',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Commercial, event, and automation relationships (subscriptions, events, etc.)
    // are added in later foundation stages — intentionally not stubbed here to
    // avoid referencing tables that don't exist yet.
}
