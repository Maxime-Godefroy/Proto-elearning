<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i=0; $i < 10; $i++) {
            $user = new Users();
            $user->setIdUser($i);
            $user->setNameUser("User ".$i);
            $user->setLoginUser("user".$i);
            $user->setPassUser("user".$i);
            $user->setMailUser("user".$i."@test.test");
            $user->setLastConnexion(new \DateTime());
            $user->setTypeCompte(1);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
