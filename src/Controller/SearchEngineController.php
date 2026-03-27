<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods: ['GET','POST'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $products = [];
        $searchedWord = '';
        
        if($request->isMethod('POST')){
            $searchedWord = $request->request->get('word');
            $products = $productRepository->searchEngine($searchedWord);
          
        }
        return $this->render('search_engine/index.html.twig', [
            'products' => $products,
            'searchedWord' => $searchedWord
        ]);
    }
}
