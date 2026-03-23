<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
        {
            
        }


    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, Cart $cart): Response
    {
        $data = $cart->getcart($session);


        return $this->render('cart/index.html.twig', [
            'items' => $data['cart'],
            'total' => $data['total']
        ]);
    }

    #[Route('/cart/add/{id}/', name: "app_cart_new", methods: ['GET'])]
    public function addProductToCart(int $id, SessionInterface $session) : Response
    {
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id]=1;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id}/', name: "app_cart_product-remove", methods: ['GET'])]
    public function removeProductToCart(int $id, SessionInterface $session) : Response
    {
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/', name: "app_cart_remove", methods: ['GET'])]
    public function remove(SessionInterface $sessionInterface) : Response
    {
        $sessionInterface->set('cart', []);

        return $this->redirectToRoute('app_cart');
    }
}
