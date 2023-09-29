<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController {
    #[Route('/products', name: 'app_products_list')]
    public function list(ProduitRepository $pr): Response {
        return $this->render('product/list.html.twig', [
            'products' => $pr->findAll(),
        ]);
    }

    #[Route('/products/{id}', name: 'app_products_details')]
    public function details(Produit $produit): Response {
        return $this->render('product/details.html.twig', [
            'product' => $produit,
        ]);
    }
}
