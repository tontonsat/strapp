<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\User;
use Faker;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        
        for ($i=0; $i < 100; $i++) { 

            $user = new User();
            $user->setPassword('dontTell')
                ->setDateSignup(new \Datetime())
                ->setCurrentLocation('2.3480874152280933,48.87062501694089')
                ->setEmail($faker->email)
                ->setName($faker->firstNameMale)
                ->setLastname($faker->lastname)
                ->setUsername($faker->username)
                ->setMood($faker->sentence($nbWords = 4, $variableNbWords = true))
                ->setBio($faker->sentence($nbWords = 10, $variableNbWords = true))
                ->setRatingWriter(0)
                ->setRatingReader(0);

            $manager->persist($user);
        }
        for ($i=0; $i < 200; $i++) { 

            $user = new User();
            $user->setPassword('dontTell')
                ->setDateSignup(new \Datetime())
                ->setCurrentLocation('7.759855265293226,48.592819805226185')
                ->setEmail($faker->email)
                ->setName($faker->firstNameFemale)
                ->setLastname($faker->lastname)
                ->setUsername($faker->username)
                ->setMood($faker->sentence($nbWords = 4, $variableNbWords = true))
                ->setBio($faker->sentence($nbWords = 10, $variableNbWords = true))
                ->setRatingWriter(0)
                ->setRatingReader(0);

            $manager->persist($user);
        }
        for ($i=0; $i < 100; $i++) { 

            $user = new User();
            $user->setPassword('dontTell')
                ->setDateSignup(new \Datetime())
                ->setCurrentLocation('7.759855265293226,48.592819805226185')
                ->setEmail($faker->email)
                ->setName($faker->firstNameMale)
                ->setLastname($faker->lastname)
                ->setUsername($faker->username)
                ->setMood($faker->sentence($nbWords = 4, $variableNbWords = true))
                ->setBio($faker->sentence($nbWords = 10, $variableNbWords = true))
                ->setRatingWriter(0)
                ->setRatingReader(0);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
