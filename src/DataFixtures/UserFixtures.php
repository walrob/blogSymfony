<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setName('Pablo Lescano');
        $user1->setUsername('pablo');
        $user1->setEmail('ellescano@gmail.org');
        $user1->setPlainPassword('lescano');
        $user1->addRole('ROLE_USER');
        $user1->setEnabled(true);
            

        $user2 = new User();
        $user2->setName('Willy Polvoron');
        $user2->setUsername('willy');
        $user2->setEmail('elpolvoron@gmail.org');
        $user2->setPlainPassword('polvoron');
        $user2->addRole('ROLE_USER');
        $user2->setEnabled(true);

        $user = new User();
        $user->setName('Usuario Prueba');
        $user->setUsername('user');
        $user->setEmail('user@email.org');
        $user->setPlainPassword('user');
        $user->addRole('ROLE_USER');
        $user->setEnabled(true);

        $admin = new User();
        $admin->setName('ADMIN');
        $admin->setUsername('admin');
        $admin->setEmail('admin@email.org');
        $admin->setPlainPassword('admin');
        $admin->addRole('ROLE_ADMIN');
        $admin->setEnabled(true);

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user);
        $manager->persist($admin);

        $manager->flush();

        $this->addReference('user-1', $user1);
        $this->addReference('user-2', $user2);
    }
}