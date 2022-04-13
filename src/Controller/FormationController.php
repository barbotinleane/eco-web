<?php

namespace App\Controller;

use App\Entity\LessonDone;
use App\Form\SearchType;
use App\Repository\FormationRepository;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();
        $search = $this->createForm(SearchType::class);

        $arrayOfFormations = $this->formationsToArrayWithProgress($formations, $formationRepository);

        return $this->render('formation/index.html.twig', [
            'formations' => $arrayOfFormations,
            'search' => $search->createView(),
        ]);
    }

    #[Route('/formations/{formation}', name: 'app_formation_view')]
    public function formation(FormationRepository $formationRepository, string $formation): Response
    {
        $formation = $formationRepository->find($formation);
        $formationWithProgress = $this->formationsToArrayWithProgress($formation, $formationRepository);

        $sections = $formation->getSections();
        $lessonsDone = $this->getUser()->getLessonDones();
        $lessonsDoneId = [];

        foreach($lessonsDone as $lessonDone) {
            $lessonsDoneId[] = $lessonDone->getLesson()->getId();
        }

        $lessons = [];
        foreach($sections as $section) {
            $lessons[] = [
                'id' => $section->getId(),
                'lessons' => $section->getLessons(),
            ];
        }

        return $this->render('formation/view.html.twig', [
            'formation' => $formationWithProgress,
            'sections' => $sections,
            'lessons' => $lessons,
            'lessonsDone' => $lessonsDoneId,
        ]);
    }

    #[Route('/formations/{formation}/{lesson}', name: 'app_formation_lesson')]
    public function lesson(LessonRepository $lessonRepository, $formation, $lesson): Response
    {
        $lessonObject = $lessonRepository->find($lesson);
        $lessonsDone = $this->getUser()->getLessonDones();
        $done = 0;

        foreach($lessonsDone as $lessonDone) {
            if($lesson == $lessonDone->getLesson()->getId()) {
                $done = 1;
            }
        }

        return $this->render('formation/lesson.html.twig', [
            'lesson' => $lessonObject,
            'formation' => $formation,
            'done' => $done,
        ]);
    }

    #[Route('/done/{formation}/{lesson}', name: 'app_formation_lesson_done')]
    public function lessonDone(LessonRepository $lessonRepository, EntityManagerInterface $entityManager, $formation, $lesson): Response
    {
        $lesson = $lessonRepository->find($lesson);
        $lessonDone = new LessonDone();
        $lessonDone->setLesson($lesson);
        $lessonDone->setLearner($this->getUser());
        $entityManager->persist($lessonDone);
        $entityManager->flush();

        return $this->redirectToRoute('app_formation_view', ['formation' => $formation]);
    }

    #[Route('/json/formations/{query}', name: 'app_formation_search')]
    public function search(FormationRepository $formationRepository, string $query) {
        if($query === "all") {
            $formations = $formationRepository->findAll();
        } else {
            $formations = $formationRepository->findByQuery($query);
        }

        $arrayOfFormations = $this->formationsToArrayWithProgress($formations, $formationRepository);

        return $this->json([
            "formations" => $arrayOfFormations
        ]);
    }

    #[Route('/json/formation/{filter}', name: 'app_formation_filter')]
    public function filter(FormationRepository $formationRepository, string $filter) {
        if($this->getUser()) {
            if ($filter === "current") {
                $formations = $formationRepository->findCurrent($this->getUser()->getId());
            } elseif ($filter === "done") {
                $formations = $formationRepository->findDone($this->getUser()->getId());
            } else {
                $formations = $formationRepository->findAll();
            }
        } else {
            $formations = $formationRepository->findAll();
        }

        $arrayOfFormations = $this->formationsToArrayWithProgress($formations, $formationRepository);

        return $this->json([
            "formations" => $arrayOfFormations
        ]);
    }

    public function formationsToArrayWithProgress($formations, FormationRepository $formationRepository) {
        if($this->getUser()) {
            if (in_array('ROLE_LEARNER', $this->getUser()->getRoles())) {
                $progress = $formationRepository->findLearnerProgressForEachFormation($this->getUser()->getId());

                $arrayOfFormations = [];
                if(is_array($formations)) {
                    foreach ($formations as $formation) {
                        $formationWritten = false;
                        foreach ($progress as $formationProgress) {
                            if($formation->getId() == $formationProgress['id']) {
                                $arrayOfFormations[] = $formation->toArray();
                                $arrayOfFormations[array_key_last($arrayOfFormations)]['progress'] = $formationProgress['progress'];
                                $formationWritten = true;
                            }
                        }
                        if ($formationWritten === false) {
                            $arrayOfFormations[] = $formation->toArray();
                        }
                    }
                } else {
                    $formationWritten = false;
                    foreach ($progress as $formationProgress) {
                        if($formations->getId() == $formationProgress['id']) {
                            $arrayOfFormations = $formations->toArray();
                            $arrayOfFormations['progress'] = $formationProgress['progress'];
                            $formationWritten = true;
                        }
                    }
                    if ($formationWritten === false) {
                        $arrayOfFormations = $formations->toArray();
                    }
                }
            }
        } else {
            $arrayOfFormations = [];
            if(is_array($formations)) {
                foreach ($formations as $formation) {
                    $arrayOfFormations[] = $formation->toArray();
                }
            } else {
                $arrayOfFormations = $formations->toArray();
            }
        }
        return $arrayOfFormations;
    }
}
