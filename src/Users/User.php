<?php

namespace App\Users;

use App\Organisations\Organisation;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $id;
    private $username;
    private $password;
    private $plainPassword;
    private $roles;
    private $active;
    private $resetToken;
    private $resetExpiresAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (string) $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = (string) $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = (string) $password;
    }

    public function getRoles()
    {
        return explode(',', $this->roles);
    }

    public function setRoles(array $roles)
    {
        foreach ($roles as $role) {
            if (!in_array($role, UserRoles::all())) {
                throw new \Exception('User role '.$role.' is invalid');
            }
        }

        $this->roles = implode(',', $roles);
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Generates new reset token which expires in specified period of time.
     */
    public function generateResetToken(\DateInterval $interval): string
    {
        $now = new \DateTime();
        $this->resetToken = Uuid::uuid4()->getHex();
        $this->resetExpiresAt = $now->add($interval)->getTimestamp();

        return $this->resetToken;
    }

    /**
     * Clears current reset token.
     */
    public function clearResetToken(): self
    {
        $this->resetToken = null;
        $this->resetExpiresAt = null;

        return $this;
    }

    /**
     * Checks whether specified reset token is valid.
     */
    public function isResetTokenValid(string $token): bool
    {
        return $this->resetToken === $token && null !== $this->resetExpiresAt && $this->resetExpiresAt > time();
    }

    function equals(UserInterface $user)
    {
        // TODO: Implement equals() method.
    }
}
