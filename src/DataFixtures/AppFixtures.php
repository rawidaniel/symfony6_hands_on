<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasherInterface
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user1 = new User();
        $user1->setEmail('test@test.com');
        $user1->setPassword($this->userPasswordHasherInterface->hashPassword($user1, '123456'));
        $manager->persist($user1);


        $user2 = new User();
        $user2->setEmail('test2@test.com');
        $user2->setPassword($this->userPasswordHasherInterface->hashPassword($user2, '123456'));
        $manager->persist($user2);

        $microPost1 = new MicroPost();
        $microPost1->setTitle('Welcome to poland!');
        $microPost1->setText('welcome to poland!');
        $microPost1->setCreatedAt(new DateTime());
        $microPost1->setAuthor($user1);
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Welcome to USA!');
        $microPost2->setText('welcome to USA!');
        $microPost2->setCreatedAt(new DateTime());
        $user2->addPost($microPost2);
        $manager->persist($microPost2);

        $microPost3 = new MicroPost();
        $microPost3->setTitle('Welcome to Germany!');
        $microPost3->setText('welcome to Germany!');
        $microPost3->setCreatedAt(new DateTime());
        $microPost3->setAuthor($user1);
        $manager->persist($microPost3);

        $manager->flush();
    }
}
