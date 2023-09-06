<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SettingsProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        
    }


    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('ROLE_VERIFIED')]
    public function profile(Request $request): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);

            $this->entityManager->persist($userProfile);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your user Profile settings were saved!');

            return $this->redirectToRoute('app_settings_profile');

        }
        return $this->render('settings_profile/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/settings/profile-image', name: 'app_settings_profile_image')]
    #[IsGranted('ROLE_VERIFIED')]
    public function profileImage()
    {
        $form = $this->createForm(ProfileImageType::class);

        return $this->render('settings_profile/profile_image.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
