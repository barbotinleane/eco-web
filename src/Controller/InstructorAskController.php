<?php

namespace App\Controller;

use App\Entity\InstructorAsk;
use App\Form\InstructorAskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstructorAskController extends AbstractController
{
    #[Route('/instructor/ask', name: 'app_instructor_ask')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $ask = new InstructorAsk();
        $form = $this->createForm(InstructorAskType::class, $ask);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ask);
            $em->flush();

            return $this->redirectToRoute('app_instructor_ask');
        }

        return $this->render('instructor_ask/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
