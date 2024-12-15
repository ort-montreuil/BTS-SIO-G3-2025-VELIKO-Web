<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        // $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        //---------Notre ADMIN------------

        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setEmail('toto@dev.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, 'Motdepasse123//'));
        $user->setVerified(1);
        $user->setDateNaissance(new \DateTime('1990-01-01'));
        $user->setAdresse('1 rue de la rue');
        $user->setVille('Paris');
        $user->setCodePostal('75000');
        $user->setNom('Toto');
        $user->setPrenom('Mr');
        $user->setBooleanChangerMdp(0);
        $manager->persist($user);


        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail("user-$i@gmail.com");
            $user->setRoles([]);
            $user->setPassword($this->hasher->hashPassword($user, 'Motdepasse123/'));
            $user->setVerified(true);
            $user->setDateNaissance(new \DateTime('1990-01-01'));
            $user->setAdresse('1 rue de la rue');
            $user->setVille('Paris');
            $user->setCodePostal('75000');
            $user->setNom('Toto');
            $user->setPrenom('Mr');
            $user->setToken(null);
            $user->setBooleanChangerMdp(0);
            $manager->persist($user);
        }


        $manager->flush();
    }
}