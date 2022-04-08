<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $admin = new User();
        $admin->setPseudo($faker->userName);
        $admin->setEmail($faker->email);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'chielzihslj7557q5q!shj:q5'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $manager->flush();

        $this->addReference(User::class.'_admin', $admin);

        for ($i=0; $i<2; $i++) {
            $formator = new User();
            $formator->setPseudo($faker->userName);
            $formator->setEmail($faker->email);
            $formator->setPassword($this->passwordHasher->hashPassword($formator, $faker->password));
            $formator->setRoles(['ROLE_FORMATOR']);
            $manager->persist($formator);

            $this->addReference(User::class.'_formator_' . $i, $formator);
        }
        $manager->flush();

        for ($i=0; $i<3; $i++) {
            $learner = new User();
            $learner->setPseudo($faker->userName);
            $learner->setEmail($faker->email);
            $learner->setPassword($this->passwordHasher->hashPassword($learner, $faker->password));
            $learner->setRoles(['ROLE_LEARNER']);
            $manager->persist($learner);

            $this->addReference(User::class.'_learner_' . $i, $learner);
        }
        $manager->flush();
    }
}
