<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Message;
use App\Entity\Group;
use App\Entity\UserGroup;
use App\Entity\Course;
use App\Entity\Note;
use App\Entity\CoursePart;
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
            $user->setUsername("user".$i);
            $user->setFirstname("User");
            $user->setLastname($i);
            $user->setEmail("user".$i."@test.test");
            $user->setColor("FFFFF");
            $user->setRoles(["ROLE_USER"]);
            $user->setDateCreation(new \DateTime());
            $user->setPassword(password_hash('user'.$i, PASSWORD_BCRYPT, ["cost" => 10]));
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

        for ($i = 1; $i <= 8; $i++) {
        
            $userReference = $this->getReference('user_'.$i); 

            $group = new Group();
            $group->setName("Group ".$i);
            $group->setProfesseurId($userReference);
            $group->setColor("FFFFF");
            $manager->persist($group);
        
            $this->addReference($i, $group);
        }

        for ($i = 1; $i <= 8; $i++) {

            $userReference = $this->getReference('user_'.$i); 
            $groupReference = $this->getReference($i); 

            $user_group = new UserGroup();
            $user_group->setIdUser($userReference);
            $user_group->setIdGroup($groupReference);
            $manager->persist($user_group);
        }


        for ($i = 1; $i <= 5; $i++) {

            $userReference = $this->getReference('user_'.$i); 
            $groupReference = $this->getReference($i); 

            $course = new Course();
            $course->setName("Course ".$i);
            $course->setDisponibility(new \DateTime('20-07-2023'));
            $course->setAssignedGroups($groupReference);
            $course->setCreatedBy($userReference);
            $manager->persist($course);
        
            $this->addReference('Course '.$i, $course);
        }

        for ($i = 1; $i <= 3; $i++) {

            $userReference = $this->getReference('user_'.$i); 
            $courseReference = $this->getReference('Course '.$i); 

            $note = new Note();
            $note->setValue(20);
            $note->setValidate(1);
            $note->setNbTentative(16);
            $note->setUserId($userReference);
            $note->setCourseId($courseReference);
            $note->setGivenAt(new \DateTime('20-07-2023'));
            $manager->persist($note);
        }

        for ($i = 1; $i <= 3; $i++) {
 
            $courseReference = $this->getReference('Course '.$i); 

            $course_part = new CoursePart();
            $course_part->setName("CoursePart ".$i);
            $course_part->setCourseId($courseReference);
            $course_part->setContent("Yo !");
            $manager->persist($course_part);
        }

        $manager->flush();
    }
}