<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Service\FormationResultFormater;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FormationResultFormater $formationResultFormater, FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findThree();

        $arrayOfFormations = $formationResultFormater->formationsToArrayWithProgress($formations, $formationRepository);
        return $this->render('base/index.html.twig', [
            'formations' => $arrayOfFormations,
        ]);
    }
}
