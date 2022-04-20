<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\LessonDone;
use App\Form\FormationType;
use App\Form\SearchType;
use App\Repository\FormationRepository;
use App\Repository\LessonRepository;
use App\Service\FileUploader;
use App\Service\FormationResultFormater;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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
        $quiz = [];
        foreach($sections as $section) {
            $lessons[] = [
                'id' => $section->getId(),
                'lessons' => $section->getLessons(),
            ];
            if($section->getQuestions()->isEmpty()) {
                $quiz[$section->getId()] = 0;
            } else {
                $quiz[$section->getId()] = 1;
            }
        }

        return $this->render('formation/view.html.twig', [
            'formation' => $formationWithProgress,
            'sections' => $sections,
            'lessons' => $lessons,
            'lessonsDone' => $lessonsDoneId,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/account/add/formation', name: 'app_add_formation')]
    public function addFormation(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $originalSections = new ArrayCollection();

        foreach ($formation->getSections() as $section) {
            $originalSections->add($section);
        }

        $editForm = $this->createForm(FormationType::class, $formation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the Task
            foreach ($originalSections as $section) {
                if (false === $formation->getSections()->contains($section)) {
                    // remove the Task from the Tag
                    $formation->getSections()->removeElement($section);

                    $entityManager->persist($formation);
                }
            }

            /** @var UploadedFile $imageFile */
            $imageFile = $editForm->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $formation->setImage($imageFileName);
            }

            $formation->setAuthor($this->getUser());

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_update_formation', [
                'id' => $formation->getId()
            ]);
        }

        return $this->render('formation/add.html.twig', [
            'form' => $editForm->createView(),
            'update' => 0,
        ]);
    }

    #[Route('/account/update/formation/{id}', name: 'app_update_formation', requirements: ['id' => '\d+'])]
    public function updateFormation(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager, $id = null): Response
    {
        $formation = null;
        if($id === null) {
            $formation = new Formation();
        } else if (null === $formation = $entityManager->getRepository(Formation::class)->find($id)) {
            $formation = new Formation();
        }

        $originalSections = new ArrayCollection();

        foreach ($formation->getSections() as $section) {
            $originalSections->add($section);
        }

        $editForm = $this->createForm(FormationType::class, $formation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the Task
            foreach ($originalSections as $section) {
                if (false === $formation->getSections()->contains($section)) {
                    // remove the Task from the Tag
                    $formation->getSections()->removeElement($section);

                    $entityManager->persist($formation);
                }
            }

            /** @var UploadedFile $brochureFile */
            $imageFile = $editForm->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $formation->setImage($imageFileName);
            }

            $formation->setAuthor($this->getUser());

            $entityManager->persist($formation);
            $entityManager->flush();

        }

        return $this->render('formation/add.html.twig', [
            'form' => $editForm->createView(),
            'update' => 1,
        ]);
    }

    #[Route('/account/delete/formation/{id}', name: 'app_delete_formation')]
    public function deleteFormation(EntityManagerInterface $entityManager, $id): Response
    {
        $formation = $entityManager->getRepository(Formation::class)->find($id);
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirectToRoute('app_account');
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
