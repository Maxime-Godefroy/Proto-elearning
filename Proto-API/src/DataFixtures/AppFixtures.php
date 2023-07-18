<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // ->persist($product);

        for ($i=1; $i <= 10; $i++) {
            $user = new Users();
            $user->setUsername("User ".$i);
            $user->setFirstname("User");
            $user->setLastname($i);
            $user->setEmail("user".$i."@test.test");
            $user->setColor("FFFFF");
            $user->setType(1);
            $user->setDateCreation(new \DateTime());
            $user->setPassword('user'.$i);
            $manager->persist($user);

            $this->addReference('user_'.$i, $user);
        }

        for ($i = 1; $i <= 5; $i++) {
            $userReference = $this->getReference('user_'.$i); 

            $message = new Message();
            $message->setFromUser($userReference);
            $message->setToUser($userReference);
            $message->setSentAt(new \DateTime('20-07-2023'));
            $message->setContent("Yo !");
            $manager->persist($message);
        }

        $manager->flush();
    }
}