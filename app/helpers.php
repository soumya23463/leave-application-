<?php

use App\Models\User;

if (! function_exists('authUser')) {
    function authUser(): ?User
    {
        $user = auth()->user();
        return $user instanceof User ? $user : null;
    }
}

if (! function_exists('authId')) {
    function authId(): ?int
    {
        return auth()->id();
    }
}

if (! function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return authUser()?->isAdmin() ?? false;
    }
}

if (! function_exists('isSuperAdmin')) {
    function isSuperAdmin(): bool
    {
        return authUser()?->isSuperAdmin() ?? false;
    }
}

if (! function_exists('isEmployee')) {
    function isEmployee(): bool
    {
        return authUser()?->isEmployee() ?? false;
    }
}
