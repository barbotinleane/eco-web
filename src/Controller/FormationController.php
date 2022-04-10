<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();
        $search = $this->createForm(SearchType::class);

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
            'search' => $search->createView(),
        ]);
    }

    #[Route('/formations/{query}', name: 'app_formation_search')]
    public function search(FormationRepository $formationRepository, string $query) {
        if($query === "all") {
            $formations = $formationRepository->findAll();
        } else {
            $formations = $formationRepository->findByQuery($query);
        }

        $arrayOfFormations = [];
        foreach ($formations as $formation) {
            $arrayOfFormations[] = $formation->toArray();
        }

        return $this->json([
            "formations" => $arrayOfFormations
        ]);
    }
}
