<?php


namespace  App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HelloController extends AbstractController
{

    private array $messages = [
        ['message' => 'Hello', 'created' =>'2023/07/12'],
        ['message' => 'Hi', 'created' =>'2023/05/12'],
        ['message' => 'BYE', 'created' =>'2022/06/12']
    
    ];

    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = new User();
        $user->setEmail('rawi@gmail.com');
        $user->setPassword('12345');
        $em->persist($user);
        $em->flush();
        // $userProfile = new UserProfile();
        // $userProfile->setUser($user);
        // $em->persist($userProfile);
        // $em->flush();

        // $profile = $em->getRepository(UserProfile::class)->findOneBy(['id' => 1]);
        // $em->remove($profile);
        // $em->flush();
        return $this->render('hello/index.html.twig', ['messages' =>$this->messages, 'limit' =>3]);
        // return new Response();
        // implode(', ', array_slice($this->messages, 0, $limit))
    }

    #[Route('/messages/{id}', name: "app_show_one", requirements: ['id' => '\d+'])]
    public function showOne(int $id): Response
    {
        return $this->render('hello/show_one.html.twig', ['message' => $this->messages[$id]]);
        // return new Response($this->messages[$id]);
    }
}
