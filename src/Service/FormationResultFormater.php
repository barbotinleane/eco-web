<?php

namespace App\Service;

use App\Repository\FormationRepository;
use Symfony\Component\Security\Core\Security;

class FormationResultFormater
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function formationsToArrayWithProgress($formations, FormationRepository $formationRepository) {
        if($this->security->getUser() && in_array('ROLE_LEARNER', $this->security->getUser()->getRoles())) {
            $progress = $formationRepository->findLearnerProgressForEachFormation($this->security->getUser()->getId());

            $arrayOfFormations = [];
            if(is_array($formations)) {
                foreach ($formations as $formation) {
                    $formationWritten = false;
                    foreach ($progress as $formationProgress) {
                        if($formation->getId() == $formationProgress['id']) {
                            $arrayOfFormations[] = $formation->toArray();
                            $arrayOfFormations[array_key_last($arrayOfFormations)]['progress'] = $formationProgress['progress'];
                            $formationWritten = true;
                        }
                    }
                    if ($formationWritten === false) {
                        $arrayOfFormations[] = $formation->toArray();
                    }
                }
            } else {
                $formationWritten = false;
                foreach ($progress as $formationProgress) {
                    if($formations->getId() == $formationProgress['id']) {
                        $arrayOfFormations = $formations->toArray();
                        $arrayOfFormations['progress'] = $formationProgress['progress'];
                        $formationWritten = true;
                    }
                }
                if ($formationWritten === false) {
                    $arrayOfFormations = $formations->toArray();
                }
            }
        } else {
            $arrayOfFormations = [];
            if(is_array($formations)) {
                foreach ($formations as $formation) {
                    $arrayOfFormations[] = $formation->toArray();
                }
            } else {
                $arrayOfFormations = $formations->toArray();
            }
        }
        return $arrayOfFormations;
    }
}