<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Lesson;
use App\Entity\Section;
use App\Form\FormationType;
use App\Form\LessonType;
use App\Form\SectionType;
use App\Repository\FormationRepository;
use App\Service\FileUploader;
use App\Service\FormationResultFormater;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(FormationRepository $formationRepository, FormationResultFormater $formationResultFormater): Response
    {
        $formations = [];
        if($this->getUser() && in_array('ROLE_FORMATOR', $this->getUser()->getRoles())) {
            $formationsResults = $formationRepository->findByAuthor($this->getUser()->getId());
            $formations = $formationResultFormater->formationsToArrayWithProgress($formationsResults,$formationRepository);
        }

        return $this->render('account/index.html.twig', [
            'formations' => $formations,
        ]);
    }
}
