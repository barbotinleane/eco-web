<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use App\Entity\LessonDone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LessonDoneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i<3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $lessonDone = new LessonDone();
                $lessonDone->setLearner($this->getReference(User::class . '_learner_' . $i));

                $formation = rand(0, 1);
                $section = rand(0, 1);
                $lessonDone->setLesson($this->getReference(Lesson::class.'_'.$formation.'_'.$section.'_'.$j));

                $manager->persist($lessonDone);
                $manager->flush();
            }
        }
    }

    public function getDependencies()
    {
        return [
            LessonFixtures::class,
        ];
    }
}
