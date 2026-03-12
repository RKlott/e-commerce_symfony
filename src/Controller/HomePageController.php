<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page', methods: ["GET"])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home_page/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_home_product_show', methods: ["GET"])]
    public function show(Product $product, ProductRepository $productRepository) : Response
    {
        $latestProducts = $productRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('home_page/show.html.twig', [
            'product' => $product,
            'latestProducts' => $latestProducts
        ]);
    }
}
