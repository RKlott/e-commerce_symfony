<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Options;

final class BillController extends AbstractController
{
    #[Route('/editor/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('bill/index.html.twig', [
            'order'=>$order,
        ]);
        $domPdf->loadHtml($html);
        $domPdf->render();
        $output = $domPdf->output(); //utilisation d'output à la place de stream

        return new Response($output, 200, [ //config d'output ici à la place de " '' "
            'Content-type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SymfSpace-Facture-' . $order->getId() . '.pdf"' //paramétrage de la "notice d'instruction" pour que le navigateur paramètre correctement le fichier
        ]);
    }
}
