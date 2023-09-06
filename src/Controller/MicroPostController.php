<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository): Response
    {
        // dd($microPostRepository->findAllPostsWithComments());
        return $this->render('micro_post/index.html.twig', [
            'posts' => $microPostRepository->findAllPostsWithComments(),
        ]);
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_topliked')]
    public function topLiked(MicroPostRepository $posts): Response
    {
        return $this->render(
            'micro_post/top_liked.html.twig',
            [
                'posts' => $posts->findAllWithMinLikes(2),
            ]
        );
    }

    #[Route('/micro-post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $posts): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render(
            'micro_post/follows.html.twig',
            [
                'posts' => $posts->findAllByAuthors(
                    $currentUser->getFollows()
                ),
            ]
        );
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function show(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('micro-post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('ROLE_VERIFIED')]
    public function add(Request $request): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // $this->denyAccessUnlessGranted('PUBLIC_ACCESS');

        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        // if ($form->isSubmitted()){

        //     dd($form->isValid());
        // }

        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            // $post->setCreatedAt(new DateTime());
            $post->setAuthor($this->getUser());

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your micro post have been added successfully!');
           return  $this->redirectToRoute('app_micro_post');
        } 
      
        
        return $this->render('micro_post/add.html.twig', ['form' => $form]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request) : Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           $post = $form->getData();

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your micro post have been updated successfully!');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/edit.html.twig', ['form' => $form, 'post' => $post]);

    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_add_comment')]
    // #[IsGranted('ROLE_COMMENTER')]
    public function addComment(MicroPost $post, Request $request, CommentRepository $commentRepository) : Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           $comment = $form->getData();
           $comment->setPost($post);
           $comment->setAuthor($this->getUser());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your comment have been adde successfully!');
            return $this->redirectToRoute('app_micro_post_show', ['post' => $post->getId()]);
        }

        return $this->render('micro_post/comment.html.twig', ['form' => $form, 'post' => $post]);

    }

}
