<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Status;
use App\Repository\StatusRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController {
    #[Route('/payment', name: 'app_payment')]
    public function index(StatusRepository $sr, CommandeRepository $cr): Response {

        $statusPanier = $sr->find(Status::PANIER);

        $panier = $cr->findOneBy([
            'user' => $this->getUser(),
            'status' => $statusPanier
        ]);


        if (empty($panier) || $panier->getProduits()->isEmpty()) {
            $this->addFlash('warning', 'Votre panier est vide !');
            return $this->redirectToRoute('app_cart');
        }

        Stripe::setApiKey('sk_test_51L0jgaKqhxheqSjZdd8EwhBOmzw3W2bMDF0ppiMKmaxUXwxEaI8us6NOx5xnkoTVbgaH500mmICiWcyHRpeGLG0000vAALPxex');

        $tableau = [];
        foreach ($panier->getProduits() as $p) {
            $tableau[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        // Optionnel
                        'name' => $p->getNom(),
                        'images' => [
                            'https://picsum.photos/500'
                        ] // Liens ABSOLUS
                    ],
                    'unit_amount' => $p->getPrix()
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => $tableau,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_success', ['id' => $panier->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_payment_fail', ['id' => $panier->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);


        return $this->redirect($checkout_session->url);
    }

    #[Route('/payment/success/{id}', name: 'app_payment_success')]
    public function success(Commande $panier, StatusRepository $sr, EntityManagerInterface $em): Response {
        $panier->setStatus($sr->find(Status::PAYEE));
        
        $em->persist($panier);
        $em->flush();

        return new Response('Merci pour ton argent !');
    }

    #[Route('/payment/fail/{id}', name: 'app_payment_fail')]
    public function fail(Commande $panier, StatusRepository $sr, EntityManagerInterface $em): Response {
        $panier->setStatus($sr->find(Status::PANIER));
        $this->addFlash('error', 'Le paiement a échoué. Vous n\'avez pas été débité.');

        $em->persist($panier);
        $em->flush();
        
        return $this->redirectToRoute('app_cart');
    }
}
