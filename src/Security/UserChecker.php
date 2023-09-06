<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @param User $user
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user->getBannedUntil() === NULL) {
            return;
        }

        $now = new DateTime();
        if ($now < $user->getBannedUntil()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new AccessDeniedHttpException('Your user account is banned.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // if (!$user instanceof User) {
        //     return;
        // }

        // user account is expired, the user may be notified
        // if ($user->isExpired()) {
        //     throw new AccountExpiredException('...');
        // }
    }
}