<?php

namespace App\Controller;

use App\Entity\MicroPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        
    }

    #[Route('/like/{id}', name: 'app_like')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(MicroPost $post, Request $request): Response
    {
       $currentUser = $this->getUser();
       $post->addLikedBy($currentUser);
       $this->entityManager->persist($post);
       $this->entityManager->flush();

       return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unlike/{id}', name: 'app_unlike')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    public function unlike(MicroPost $post, Request $request): Response
    {
        $currentUser = $this->getUser();
        $post->removeLikedBy($currentUser);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
