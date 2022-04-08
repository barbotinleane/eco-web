<?php

namespace App\DataFixtures;

use App\Entity\InstructorAsk;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InstructorAskFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $instructorAsk = new InstructorAsk();
            $instructorAsk->setName($faker->lastName);
            $instructorAsk->setFirstName($faker->firstName);
            $instructorAsk->setEmail($faker->email);
            $instructorAsk->setDescription($faker->sentence);

            $formator = new User();
            $formator->setPseudo('');
            $formator->setEmail($instructorAsk->getEmail());
            $formator->setPassword('');
            $formator->setRoles([]);

            $instructorAsk->setPassword($this->passwordHasher->hashPassword($formator, $faker->password));

            $manager->persist($instructorAsk);
            $manager->flush();
        }
    }
}
