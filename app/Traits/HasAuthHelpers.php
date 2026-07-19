<?php

namespace App\Traits;

use App\Models\User;

trait HasAuthHelpers
{
    protected static function authUser(): ?User
    {
        return auth()->user() instanceof User ? auth()->user() : null;
    }

    protected static function authId(): ?int
    {
        return auth()->id();
    }

    protected static function isAdmin(): bool
    {
        return static::authUser()?->isAdmin() ?? false;
    }

    protected static function isEmployee(): bool
    {
        return static::authUser()?->isEmployee() ?? false;
    }
}
