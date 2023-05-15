<?php

namespace App\Controller;
use App\Repository\UtilisateurAdresseRepository;
use App\Service\GetFacturationService;
use App\Service\GetReferenceService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacturationPdfController extends AbstractController
{
    private GetFacturationService $facturationService;
    private GetReferenceService $ReferenceService;
    private UtilisateurAdresseRepository $adresses;
    private RequestStack $request;
    public function __construct(
                                GetReferenceService $ReferenceService,
                                GetFacturationService $facturationService,
                                UtilisateurAdresseRepository $adresses,
                                RequestStack $request
    )
    {
        $this->facturationService = $facturationService;
        $this->ReferenceService = $ReferenceService;
        $this->adresses = $adresses;
        $this->request = $request;
    }

    /**
     * @Route("/facturation_pdf/{id}", name="facturation_pdf")
     */
    public function usersDataDownload()
    {

        $allPrice = $this->facturationService->trouve();

        $id= $this->request->getCurrentRequest()->get('id');

        // On définit les options du PDF

        $pdfOptions = new Options();

        // Police par défaut

        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // On instancie Dompdf

        $dompdf = new Dompdf($pdfOptions);

        $allOrder = $this->ReferenceService->reference();

        $adresse = $this->request->getSession()->get('adresse');

        $adressedelivraison = $this->adresses->find($adresse);

        $allOrder = $this->ReferenceService->referenceByCommande($id);

        $allPrice = $this->facturationService->trouve();

        // On génère le html

        $html = $this->renderView('facture/facturePDF.html.twig', [
            'allPrice' => $allPrice,
            'allOrder' => $allOrder,
            'adressedelivraison' => $adressedelivraison
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier par id user

        $fichier = 'commande-client-numero-' . $this->getUser()->getId() . '.pdf';

        // On envoie le PDF au navigateur

        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        return new Response();
    }

    /**
     * @Route("/facturation_pdf_commande/{id}", name="facturation_pdf_commande")
     */
    public function facturationpdfcommande()
    {
        $id= $this->request->getCurrentRequest()->get('id');

        $allOrder = $this->ReferenceService->reference();
        $allPrice = $this->facturationService->trouve();

        // On définit les options du PDF

        $pdfOptions = new Options();

        // Police par défaut

        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // On instancie Dompdf

        $dompdf = new Dompdf($pdfOptions);
        $allOrder = $this->ReferenceService->referenceByCommande($id);
        $adressedelivraison = $this->adresses->findByUserId($this->getUser()->getId());
        $allPrice = $this->facturationService->orderFacture($id);

        // On génère le html
        $html = $this->renderView('facture/factureHistoryPDF.html.twig', [
            'allPrice' => $allPrice,
            'allOrder' => $allOrder,
            'adressedelivraison' => $adressedelivraison
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier

        $fichier = 'commande-client-numero-' . $this->getUser()->getId() . '.pdf';

        // On envoie le PDF au navigateur

        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        return new Response();
    }

    /**
     * @Route("/Admin_facturation_pdf_commande/{id}/{idadresse}", name="Admin_facturation_pdf_commande")
     */
    public function facturationpdfcommandeAdmin()
    {
        $id= $this->request->getCurrentRequest()->get('id');

        $idadresse= $this->request->getCurrentRequest()->get('idadresse');

        $allOrder = $this->ReferenceService->reference();

        $allPrice = $this->facturationService->trouve();

        // On définit les options du PDF

        $pdfOptions = new Options();

        // Police par défaut

        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // On instancie Dompdf

        $dompdf = new Dompdf($pdfOptions);

        $allOrder = $this->ReferenceService->referenceByCommande($id);

        $adressedelivraison = $this->adresses->findByUserId($idadresse);

        $allPrice = $this->facturationService->orderFacture($id);

        // On génère le html

        $html = $this->renderView('facture/factureHistoryPDF.html.twig', [
            'allPrice' => $allPrice,
            'allOrder' => $allOrder,
            'adressedelivraison' => $adressedelivraison
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier

        $fichier = 'commande-numero-' . $id . '.pdf';

        // On envoie le PDF au navigateur

        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        return new Response();
    }
}
