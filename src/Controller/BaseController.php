<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Service\FormationResultFormater;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MailerInterface $mailer, FormationResultFormater $formationResultFormater, FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findThree();

        $email = (new Email())
            ->from('leanebn@gmail.com')
            ->to('leanebn@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        $arrayOfFormations = $formationResultFormater->formationsToArrayWithProgress($formations, $formationRepository);
        return $this->render('base/index.html.twig', [
            'formations' => $arrayOfFormations,
        ]);
    }
}
