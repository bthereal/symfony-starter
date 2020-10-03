<?php

namespace App\Users;

final class UserRole
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_CUSTOMER_SUPPORT = 'ROLE_CUSTOMER_SUPPORT';

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name The role name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
