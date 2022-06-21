<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Section;
use App\Form\FormationType;
use App\Form\QuestionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    #[Route('/formation/{formationId}/section/{sectionId}/quiz', name: 'app_quiz')]
    public function index($formationId, $sectionId, EntityManagerInterface $entityManager): Response
    {
        $section = $entityManager->getRepository(Section::class)->find($sectionId);

        return $this->render('quiz/index.html.twig', [
            'section' => $section,
            'formationId' => $formationId,
            'sectionId' => $sectionId,
        ]);
    }

    #[Route('/formation/{formationId}/section/{sectionId}/quiz/view', name: 'app_quiz_view')]
    public function view($formationId, $sectionId, EntityManagerInterface $entityManager, Request $request)
    {
        $section = $entityManager->getRepository(Section::class)->find($sectionId);
        $questions = $section->getQuestions();
        $result = [];

        if($request->isMethod('post')) {
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('submit-quiz', $submittedToken)) {
                $result = $request->request->all();
            }
        }

        return $this->render('quiz/view.html.twig', [
            'questions' => $questions,
            'formationId' => $formationId,
            'sectionId' => $sectionId,
            'result' => $result
        ]);
    }

    #[Route('/formation/{formationId}/section/{sectionId}/quiz/add', name: 'app_add_question')]
    public function add(Request $request, EntityManagerInterface $entityManager, $formationId, $sectionId): Response
    {
        $question = new Question();
        $originalAnswers = new ArrayCollection();

        foreach ($question->getAnswers() as $answer) {
            $originalAnswers->add($answer);
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($originalAnswers as $answer) {
                if (false === $question->getAnswers()->contains($answer)) {
                    $question->getAnswers()->removeElement($answer);

                    $entityManager->persist($question);
                }
            }

            $question->setSection($entityManager->getRepository(Section::class)->find($sectionId));

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_update_question', [
                'formationId' => $formationId,
                'sectionId' => $sectionId,
                'questionId' => $question->getId()
            ]);
        }

        return $this->render('quiz/add.html.twig', [
            'form' => $form->createView(),
            'formationId' => $formationId,
            'sectionId' => $sectionId,
            'update' => 0
        ]);
    }

    #[Route('/formation/{formationId}/section/{sectionId}/quiz/{questionId}', name: 'app_update_question')]
    public function update($formationId, $sectionId, $questionId, EntityManagerInterface $entityManager, Request $request): Response
    {
        $question = null;
        if($questionId === null) {
            $question = new Question();
        } else if (null === $question = $entityManager->getRepository(Question::class)->find($questionId)) {
            $question = new Question();
        }
        $originalAnswers = new ArrayCollection();

        foreach ($question->getAnswers() as $answer) {
            $originalAnswers->add($answer);
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($originalAnswers as $answer) {
                if (false === $question->getAnswers()->contains($answer)) {
                    $question->getAnswers()->removeElement($answer);

                    $entityManager->persist($question);
                }
            }

            $question->setSection($entityManager->getRepository(Section::class)->find($sectionId));

            $entityManager->persist($question);
            $entityManager->flush();
        }

        return $this->render('quiz/add.html.twig', [
            'form' => $form->createView(),
            'formationId' => $formationId,
            'sectionId' => $sectionId,
            'update' => 1
        ]);
    }

    #[Route('/formation/{formationId}/section/{sectionId}/quiz/delete/{questionId}', name: 'app_delete_question')]
    public function delete($formationId, $sectionId, $questionId, EntityManagerInterface $entityManager): Response
    {
        $question = $entityManager->getRepository(Question::class)->find($questionId);
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_quiz', [
            'formationId' => $formationId,
            'sectionId' => $sectionId
        ]);
    }
}
