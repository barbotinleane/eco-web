<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LessonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 2; $j++) {
                for ($k = 0; $k < 3; $k++) {
                    $lesson = new Lesson();
                    $lesson->setTitle($faker->sentence(4));
                    $lesson->setContent($faker->paragraphs(1, true));
                    $lesson->setSection($this->getReference(Section::class.'_'.$i.'_'.$j));
                    $manager->persist($lesson);
                    $manager->flush();

                    $this->addReference(Lesson::class.'_'.$i.'_'.$j.'_'.$k, $lesson);
                }
            }
        }
    }

    public function getDependencies()
    {
        return [
            SectionFixtures::class,
        ];
    }
}
