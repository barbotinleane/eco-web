<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FormationFixtures extends Fixture implements DependentFixtureInterface
{
    /***
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=0; $i<2; $i++) {
            $formation = new Formation();
            $formation->setTitle($faker->sentence(3));
            $formation->setAuthor($this->getReference(User::class.'_formator_'.$i));
            $manager->persist($formation);
            $manager->flush();

            $this->addReference(Formation::class.'_'.$i, $formation);
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
