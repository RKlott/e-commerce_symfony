<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/pay/success', name: 'app_stripe_success')]
    public function success(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->render('stripe/index.html.twig', []);
    }

    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', []);
    }

    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);

        $endpoint_secret = 'whsec_7a58cb6b9909d097ee7c343f1beedc8d04f434679906d19b93329785ef91b9cf';

        $payload = $request->getContent();

        $sigHeader = $request->headers->get('Stripe-Signature');

        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;

                $filename = 'stripe-detail' . uniqid() . 'txt';
                $orderId = $paymentIntent->metadata->orderId;
                $order = $orderRepository->find($orderId);

                $cartPrice = $order->getTotalPrice();
                $stripeTotalAmount = $paymentIntent->amount / 100;
                if ($cartPrice == $stripeTotalAmount) {
                    $order->setIsPaymentCompleted(1);
                    $entityManagerInterface->flush();
                }


                file_put_contents($filename, $orderId);
                break;

            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                break;

            default:
                # code...
                break;
        }

        return new Response('Evenement reçu avec succès', 200);
    }
}
