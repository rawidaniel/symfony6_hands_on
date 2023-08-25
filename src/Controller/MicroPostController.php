<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository, EntityManagerInterface $em): Response
    {
        // $micro1 = new MicroPost();
        // $micro1->setTitle('from controller');
        // $micro1->setText('HI');
        // $micro1->setCreatedAt(new DateTime());
        // $microPostRepository->$entityManager->persist($Entity);
        // $em->persist($micro1);
        // $em->flush();

        dd($microPostRepository->findAll());
        return $this->render('micro_post/index.html.twig', [
            'controller_name' => 'MicroPostController',
        ]);
    }

    #[Route('/micro-post/{id}', name: 'app_micro_post_show')]
    public function show(MicroPost $post): Response
    {
        dd($post);
    }

}
