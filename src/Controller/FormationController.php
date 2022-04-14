<?php

namespace App\Controller;

use App\Entity\LessonDone;
use App\Form\SearchType;
use App\Repository\FormationRepository;
use App\Repository\LessonRepository;
use App\Service\FormationResultFormater;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formation')]
    public function index(FormationRepository $formationRepository, FormationResultFormater $formationResultFormater): Response
    {
        $formations = $formationRepository->findAll();
        $search = $this->createForm(SearchType::class);

        $arrayOfFormations = $formationResultFormater->formationsToArrayWithProgress($formations, $formationRepository);

        return $this->render('formation/index.html.twig', [
            'formations' => $arrayOfFormations,
            'search' => $search->createView(),
        ]);
    }

    #[Route('/formations/{formation}', name: 'app_formation_view')]
    public function formation(FormationRepository $formationRepository, FormationResultFormater $formationResultFormater, string $formation): Response
    {
        $formation = $formationRepository->find($formation);
        $formationWithProgress = $formationResultFormater->formationsToArrayWithProgress($formation, $formationRepository);

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
    public function search(FormationRepository $formationRepository, FormationResultFormater $formationResultFormater, string $query) {
        if($query === "all") {
            $formations = $formationRepository->findAll();
        } else {
            $formations = $formationRepository->findByQuery($query);
        }

        $arrayOfFormations = $formationResultFormater->formationsToArrayWithProgress($formations, $formationRepository);

        return $this->json([
            "formations" => $arrayOfFormations
        ]);
    }

    #[Route('/json/formation/{filter}', name: 'app_formation_filter')]
    public function filter(FormationRepository $formationRepository, FormationResultFormater $formationResultFormater, string $filter) {
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

        $arrayOfFormations = $formationResultFormater->formationsToArrayWithProgress($formations, $formationRepository);

        return $this->json([
            "formations" => $arrayOfFormations
        ]);
    }
}
