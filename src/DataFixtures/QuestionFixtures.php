<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 2; $j++) {
                for ($k = 0; $k < 3; $k++) {
                    $question = new Question();

                    $question->setQuestion($faker->sentence);
                    $question->setSection($this->getReference('App\Entity\Section_'.$i.'_'.$j));

                    $manager->persist($question);
                    $manager->flush();

                    $this->addReference(Question::class.'_'.$i.'_'.$j.'_'.$k, $question);
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
