<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $user1 = new User();
        $user1->setUsername('APITest1');
        $user1->setPassword('$2y$04$eDhk8h8b6GDaxQrx/cP0jOGZVO1ZTk17MXe0BGREOIAQxr..VpnF6');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('APITest2');
        $user2->setPassword('$2y$04$E9uTJXsEGD0dMt4Kcq6dNuvrGIu.RnL3AzQBGhOkmhwjPKx9/6JGa');
        $manager->persist($user2);

        $manager->flush();
    }
}
