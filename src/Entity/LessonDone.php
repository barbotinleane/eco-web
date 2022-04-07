<?php

namespace App\Entity;

use App\Repository\LessonDoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LessonDoneRepository::class)]
class LessonDone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'string')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Lesson::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $lesson;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'lessonDones')]
    #[ORM\JoinColumn(nullable: false)]
    private $learner;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLearner(): ?User
    {
        return $this->learner;
    }

    public function setLearner(?User $learner): self
    {
        $this->learner = $learner;

        return $this;
    }
}
