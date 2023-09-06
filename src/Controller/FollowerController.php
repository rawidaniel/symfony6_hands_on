<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FollowerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(
        User $userToFollow,
        Request $request
    ): Response {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();

        if ($currentUser->getId() !== $userToFollow->getId()) {
            $currentUser->follow($userToFollow);
            $this->entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(
        User $userToUnfollow,
        Request $request
    ): Response {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();

        if ($currentUser->getId() !== $userToUnfollow->getId()) {
            $currentUser->unfollow($userToUnfollow);
            $this->entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
