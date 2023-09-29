<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\Status;
use App\Repository\CommandeRepository;
use App\Repository\StatusRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CartController extends AbstractController {
    #[Route('/cart', name: 'app_cart')]
    public function list(CommandeRepository $cr, StatusRepository $sr, EntityManagerInterface $em): Response {
        $statusPanier = $sr->find(Status::PANIER);

        $panier = $cr->findOneBy([
            'user' => $this->getUser(),
            'status' => $statusPanier
        ]);

        if (empty($panier)) {
            $panier = new Commande;
            $panier->setDate(new DateTime());
            $panier->setNumero('PANIER');
            $panier->setUser($this->getUser());
            $panier->setStatus($statusPanier);
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $panier
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(Produit $produit, CommandeRepository $cr, StatusRepository $sr, EntityManagerInterface $em): Response {
        $statusPanier = $sr->find(Status::PANIER);

        $panier = $cr->findOneBy([
            'user' => $this->getUser(),
            'status' => $statusPanier
        ]);

        if (empty($panier)) {
            $panier = new Commande;
            $panier->setDate(new DateTime());
            $panier->setNumero('PANIER');
            $panier->setUser($this->getUser());
            $panier->setStatus($statusPanier);
        }

        $panier->addProduit($produit);

        $em->persist($panier);
        $em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove(Produit $produit, CommandeRepository $cr, StatusRepository $sr, EntityManagerInterface $em): Response {
        $statusPanier = $sr->find(Status::PANIER);

        $panier = $cr->findOneBy([
            'user' => $this->getUser(),
            'status' => $statusPanier
        ]);

        if (!empty($panier)) {
            $panier->removeProduit($produit);
            
            $em->persist($panier);
            $em->flush();
        }

        return $this->redirectToRoute('app_cart');
    }
}
