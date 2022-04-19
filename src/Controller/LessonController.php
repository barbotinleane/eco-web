<?php

namespace App\Controller;

use App\Entity\Formation;
use Symfony\Component\Mime\MimeTypes;
use App\Entity\Lesson;
use App\Entity\Section;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonController extends AbstractController
{
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

        return $this->render('lesson/view.html.twig', [
            'lesson' => $lessonObject,
            'formation' => $formation,
            'done' => $done,
        ]);
    }

    #[Route('/account/add/formation/{formationId}/section/{sectionId}', name: 'app_add_lesson')]
    public function addLesson(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager, $formationId, $sectionId): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();
            if ($videoFile) {
                $videoFileName = $fileUploader->upload($videoFile);
                $lesson->setVideo($videoFileName);
            }

            $lesson->setSection($entityManager->getRepository(Section::class)->find($sectionId));

            $entityManager->persist($lesson);
            $entityManager->flush();

            // redirect back to some edit page
            return $this->redirectToRoute('app_update_section', [
                'formationId' => $formationId,
                'sectionId' => $sectionId
            ]);
        }

        return $this->render('lesson/index.html.twig', [
            'form' => $form->createView(),
            'update' => 0,
        ]);
    }

    #[Route('/account/update/formation/{formationId}/section/{sectionId}/lecon/{lessonId}', name: 'app_update_lesson')]
    public function updateLesson(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager, $formationId, $sectionId, $lessonId): Response
    {
        $lesson = null;
        if($lessonId === null) {
            $lesson = new Lesson();
        } else if (null === $lesson = $entityManager->getRepository(Lesson::class)->find($lessonId)) {
            $lesson = new Lesson();
        }

        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();
            if ($videoFile) {
                $videoFileName = $fileUploader->upload($videoFile);
                $lesson->setVideo($videoFileName);
            }

            $lesson->setSection($entityManager->getRepository(Section::class)->find($sectionId));

            $entityManager->persist($lesson);
            $entityManager->flush();
        }

        return $this->render('lesson/index.html.twig', [
            'form' => $form->createView(),
            'update' => 1,
        ]);
    }

    #[Route('/account/delete/formation/{formationId}/section/{sectionId}/lecon/{lessonId}', name: 'app_delete_lesson')]
    public function deleteLesson(EntityManagerInterface $entityManager, $formationId, $sectionId, $lessonId): Response
    {
        $lesson = $entityManager->getRepository(Lesson::class)->find($lessonId);
        $entityManager->remove($lesson);
        $entityManager->flush();

        return $this->redirectToRoute('app_update_section', [
            'formationId' => $formationId,
            'sectionId' => $sectionId
        ]);
    }
}
