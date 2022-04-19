<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
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

    #[Route('/account/add/{id}', name: 'app_account_add_formation', requirements: ['id' => '\d+'])]
    public function add(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager, $id = null): Response
    {
        $formation = null;
        if($id === null) {
            $formation = new Formation();
        } else if (null === $formation = $entityManager->getRepository(Formation::class)->find($id)) {
            $formation = new Formation();
        }

        $originalSections = new ArrayCollection();

        foreach ($formation->getSections() as $section) {
            $originalSections->add($section);
        }

        $editForm = $this->createForm(FormationType::class, $formation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the Task
            foreach ($originalSections as $section) {
                if (false === $formation->getSections()->contains($section)) {
                    // remove the Task from the Tag
                    $formation->getSections()->removeElement($section);

                    $entityManager->persist($formation);
                }
            }

            /** @var UploadedFile $brochureFile */
            $imageFile = $editForm->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $formation->setImage($imageFileName);
            }

            $formation->setAuthor($this->getUser());

            $entityManager->persist($formation);
            $entityManager->flush();

            // redirect back to some edit page
            return $this->redirectToRoute('app_formation');
        }

        return $this->render('account/add.html.twig', [
            'form' => $editForm->createView(),
        ]);
    }
}
