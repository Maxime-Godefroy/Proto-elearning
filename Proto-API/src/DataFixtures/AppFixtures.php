<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Activities;
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
            $user->setNameUser("User ".$i);
            $user->setLoginUser("user".$i);
            $user->setPassUser("user".$i);
            $user->setMailUser("user".$i."@test.test");
            $user->setLastConnexion(new \DateTime());
            $user->setTypeCompte(1);
            $manager->persist($user);

            $this->addReference('user_'.$i, $user);
        }

        for ($i = 1; $i <= 5; $i++) {
            $userReference = $this->getReference('user_'.$i); 

            $activity = new Activities();
            $activity->setNameActivities("Activité " . $i);
            $activity->setGestActivities($userReference);
            $activity->setEndDate(new \DateTime('20-07-2023'));
            $manager->persist($activity);
        }

        $manager->flush();
    }
}
