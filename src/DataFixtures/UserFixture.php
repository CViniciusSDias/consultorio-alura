<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('username')
            ->setPassword('$argon2i$v=19$m=1024,t=2,p=2$ajUxei80RWJtNUN2cXkvSw$3WoQDUM0Iayk+UwSzGb4Q9E/ZT0KelbUyW0aHWQhxZ8');
        $manager->persist($user);

        $manager->flush();
    }
}
