<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AnswerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 2; $j++) {
                for ($k = 0; $k < 3; $k++) {
                    for ($l = 0; $l < 3; $l++) {
                        $answer = new Answer();

                        $answer->setAnswer($faker->sentence(3));
                        $answer->setQuestion($this->getReference(Question::class.'_'.$i.'_'.$j.'_'.$k));
                        if ($k == 1) {
                            $answer->setOutcome(true);
                        } else {
                            $answer->setOutcome(false);
                        }

                        $manager->persist($answer);
                        $manager->flush();
                    }
                }
            }
        }
    }

    public function getDependencies()
    {
        return [
            QuestionFixtures::class,
        ];
    }
}
