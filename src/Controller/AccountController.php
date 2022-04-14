<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Service\FormationResultFormater;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
