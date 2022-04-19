<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\InstructorAsk;
use App\Entity\Lesson;
use App\Entity\Section;
use App\Entity\User;
use App\Form\FormationType;
use App\Form\LessonType;
use App\Form\SectionType;
use App\Repository\FormationRepository;
use App\Repository\InstructorAskRepository;
use App\Service\FileUploader;
use App\Service\FormationResultFormater;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(FormationRepository $formationRepository, FormationResultFormater $formationResultFormater, InstructorAskRepository $instructorAskRepository): Response
    {
        $formations = [];
        $instructorAsks = [];
        if($this->getUser() && in_array('ROLE_FORMATOR', $this->getUser()->getRoles())) {
            $formationsResults = $formationRepository->findByAuthor($this->getUser()->getId());
            $formations = $formationResultFormater->formationsToArrayWithProgress($formationsResults,$formationRepository);
        }

        if($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $instructorAsks = $instructorAskRepository->findAll();
        }

        return $this->render('account/index.html.twig', [
            'formations' => $formations,
            'asks' => $instructorAsks,
        ]);
    }

    #[Route('/account/accept/{askId}', name: 'app_account_accept')]
    public function acceptInstructorAsk($askId, EntityManagerInterface $entityManager)
    {
        $ask = $entityManager->getRepository(InstructorAsk::class)->find($askId);
        dump($askId);
        dump($ask);

        $newFormator = new User();
        $newFormator->setPseudo($ask->getFirstName().$ask->getName());
        $newFormator->setEmail($ask->getEmail());
        $newFormator->setPassword($ask->getPassword());
        $newFormator->setRoles(['ROLE_FORMATOR']);

        $entityManager->remove($ask);
        $entityManager->persist($newFormator);
        $entityManager->flush();

        $this->addFlash('success', 'Compte Formateur créé avec le pseudo '.$ask->getFirstName().$ask->getName());

        return $this->redirectToRoute('app_account');
    }

    #[Route('/account/refuse/{askId}', name: 'app_account_refuse')]
    public function refuseInstructorAsk($askId, EntityManagerInterface $entityManager)
    {
        $ask = $entityManager->getRepository(InstructorAsk::class)->find($askId);

        $entityManager->remove($ask);
        $entityManager->flush();

        $this->addFlash('danger', 'Demande refusée');

        return $this->redirectToRoute('app_account');
    }
}
