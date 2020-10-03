<?php

namespace App\Users;

final class UserRoles
{
    public static function all(): array
    {
        return [
            new UserRole(UserRole::ROLE_ADMIN),
            new UserRole(UserRole::ROLE_CUSTOMER_SUPPORT),
        ];
    }
}
