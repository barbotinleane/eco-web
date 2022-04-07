<?php

namespace App\Entity;

use App\Repository\LessonDoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonDoneRepository::class)]
class LessonDone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'lessonDones')]
    #[ORM\JoinColumn(nullable: false)]
    private $learner;

    #[ORM\ManyToOne(targetEntity: Lesson::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $lesson;

    public function getId(): ?int
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

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }
}
