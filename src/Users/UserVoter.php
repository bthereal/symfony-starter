<?php

namespace App\Users;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [UserPermissions::EDIT, UserPermissions::CREATE, UserPermissions::VIEW, UserPermissions::DELETE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (UserPermissions::CREATE === $attribute || UserPermissions::DELETE === $attribute) {
            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                return false;
            }
        }

        if (UserPermissions::EDIT === $attribute || UserPermissions::VIEW === $attribute) {
            // Check the role
            if ($user->getId() === $subject->getId()) {
                return true;
            }

            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                return false;
            }
        }

        return $user === $subject;
    }

    function supportsAttribute($attribute)
    {
        // TODO: Implement supportsAttribute() method.
    }

    function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }
}
