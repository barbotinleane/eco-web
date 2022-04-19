<?php

namespace App\Controller;

use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    #[Route('/account/update/formation/{formationId}/section/{sectionId}', name: 'app_update_section')]
    public function updateSection(EntityManagerInterface $entityManager, $formationId, $sectionId): Response
    {
        $section = $entityManager->getRepository(Section::class)->find($sectionId);

        return $this->render('section/index.html.twig', [
            'section' => $section,
        ]);
    }
}
