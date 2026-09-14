<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case TeamMember = 'team_member';
    case PlatformAdmin = 'platform_admin';

    public function isPlatformAdmin(): bool
    {
        return $this === self::PlatformAdmin;
    }
}
