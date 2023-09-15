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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Uuid;

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
    public function profileImage(Request $request, SluggerInterface $slugger)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileImageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $profileImageFile = $form->get('profileImage')->getData();

            if ($profileImageFile){
                $originalFileName = pathinfo(
                    $profileImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

                try{
                    $profileImageFile->move(
                        $this->getParameter('profiles_directory'),
                        $newFileName
                    );
                } catch(FileException $e)
                {}

                if ($user->getUserProfile()){
                    $profile = $user->getUserProfile();
                } else {
                    $profile = new UserProfile();
                    $user->setUserProfile($profile);
                }
                $profile->setImage($newFileName);
                $this->entityManager->persist($user);
                $this->addFlash('success', 'Your profile image was updated successfully');
                $this->entityManager->flush();

                return $this->redirectToRoute('app_settings_profile_image');
            }
        }

        return $this->render('settings_profile/profile_image.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
