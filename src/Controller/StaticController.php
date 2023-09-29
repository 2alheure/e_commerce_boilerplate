<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController {
    #[Route('/', name: 'app_home')]
    public function index(): Response {
        return $this->render('static/home.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email)
                ->from($form->get('email')->getData())
                ->to('contact@mon-super-site.com')
                ->replyTo($form->get('email')->getData())
                ->subject($form->get('subject')->getData())
                ->text($form->get('contenu')->getData());

            $mailer->send($email);
            $this->addFlash('success', 'Demande de contact envoyée avec succès.');
        }

        return $this->render('static/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
