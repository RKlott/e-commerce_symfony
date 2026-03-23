<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Service\Cart;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SessionInterface $session, ProductRepository $productRepository, EntityManagerInterface $entityManager, Cart $cart): Response
    {

        $data = $cart->getcart($session);

        $order = new Order();

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($order->isPayOnDelivery()) {


                if (!empty($data['total'])) {
                    $order->setTotalPrice($data['total']);
                    $order->setCreatedAt(new \DateTimeImmutable());
                    $entityManager->persist($order);
                    $entityManager->flush();

                    foreach ($data['cart'] as $value) {
                        $orderProduct = new OrderProducts();
                        $orderProduct->setOrder($order);
                        $orderProduct->setProduct($value['product']);
                        $orderProduct->setQuantity($value['quantity']);
                        $entityManager->persist($orderProduct);
                        $entityManager->flush();
                    }
                }




                $this->addFlash('success', 'La commande à bien été soumise.');
                $session->set('cart', []);
                return $this->redirectToRoute('app_order_message', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'total' => $data['total']
        ]);
    }

    #[Route('/order-message', name: 'app_order_message')]
    public function orderMessage(): Response
    {

        return $this->render('order/order_message.html.twig');
    }

    #[Route('/editor/order', name: 'app_orders_show')]
    public function getAllOrder(OrderRepository $repo) : Response
    {
        $orders = $repo->findAll();

        return $this->render('order/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost', methods: ['GET'])]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => 'on', 'content' => $cityShippingPrice]));
    }

    
}
    

//     #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
//     public function show(Order $order): Response
//     {
//         return $this->render('order/show.html.twig', [
//             'order' => $order,
//         ]);
//     }

//     #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
//     public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
//     {
//         $form = $this->createForm(OrderType::class, $order);
//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//             $entityManager->flush();

//             return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
//         }

//         return $this->render('order/edit.html.twig', [
//             'order' => $order,
//             'form' => $form,
//         ]);
//     }

//     #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
//     public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
//     {
//         if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->getPayload()->getString('_token'))) {
//             $entityManager->remove($order);
//             $entityManager->flush();
//         }

//         return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
//     }
// }
