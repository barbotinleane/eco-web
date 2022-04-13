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

        $arrayOfFormations = $this->formationsToArrayWithProgress($formations, $formationRepository);
        dump($arrayOfFormations);

        return $this->render('formation/index.html.twig', [
            'formations' => $arrayOfFormations,
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
                foreach ($formations as $formation) {
                    foreach ($progress as $formationProgress) {
                        if($formation->getId() == $formationProgress['id']) {
                            $arrayOfFormations[] = $formation->toArray();
                            $arrayOfFormations[array_key_last($arrayOfFormations)]['progress'] = $formationProgress['progress'];
                        } else {
                            $arrayOfFormations[] = $formation->toArray();
                        }
                    }
                }
            }
        } else {
            $arrayOfFormations = [];
            foreach ($formations as $formation) {
                $arrayOfFormations[] = $formation->toArray();
            }
        }
        return $arrayOfFormations;
    }
}
