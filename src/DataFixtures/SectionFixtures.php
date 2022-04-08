<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SectionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 2; $j++) {
                $section = new Section();
                $section->setTitle($faker->sentence(4));
                $section->setFormation($this->getReference(Formation::class . '_' . $i));

                $manager->persist($section);
                $manager->flush();

                $this->addReference(Section::class.'_'.$i.'_'.$j, $section);
            }
        }
    }

    public function getDependencies()
    {
        return [
            FormationFixtures::class,
        ];
    }
}
