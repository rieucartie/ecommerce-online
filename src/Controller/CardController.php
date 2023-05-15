<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\GetFacturationService;
use App\Service\GetReferenceService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardController extends AbstractController
{
    private MailerService $mailer;
    private GetFacturationService $facturationService;
    private GetReferenceService $ReferenceService;
    private CommandeRepository $repo;
    private EntityManagerInterface $em;

    private RequestStack $request;

    /**
     * CardController constructor.
     * @param MailerService $mailer
     */
    public function __construct(GetReferenceService $ReferenceService,
                                CommandeRepository $repo,
                                MailerService $mailer,
                                GetFacturationService $facturationService,
                                EntityManagerInterface $em,
                                RequestStack $request)
    {
        //$this->mailer = $mailer;
        $this->facturationService = $facturationService;
        $this->ReferenceService = $ReferenceService;
        $this->repo=$repo;
        $this->em=$em;
        $this->request=$request;
    }
    /**
     * @Route("/findecommande", name="findecommande")
     * @param UserRepository $repouser
     * @param CommandeRepository $repo
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function valideCart(UserRepository $repouser,
                               Request $request,
                               ProductRepository $reoprodct): Response
    {
        $session = $this->request->getSession();

        $numeroCarte = $request->request->get('numeroCarte');

        $mois_cc = (int)$request->request->get('mois_cc');

        $annee_cc = (int)$request->request->get('annee_cc');

        if ($this->verif_date_exp($mois_cc, $annee_cc)) {

            //$this->addFlash('alert alert-succes', 'la date de la carte est valable.');

            $validPaiement = $this->verifie_num_cc($numeroCarte);

            if ($validPaiement) {

                //update valider command à 1

                $idcom = $session->get('idcom');

                $this->repo->updateRelanceAvide($idcom);

                $user = $this->getUser()->getUsername();

                $recupEmailUser = $repouser->findEmailByUsername($user);

                // adapter à ses besoins avec les variables d'environnements

                /*$this->mailer->send(
                    'essai 1',
                    'monmail',
                    'monmail',
                    "email/contact.html.twig",
                    [
                        "name" => $user
                    ]
                );*/

                /* je dois ici utiliser dompdf  */

                $allPrice = $this->ReferenceService->reference();

                $allOrder = $this->facturationService->trouve();

                /* fin dompdf  */

                /* gestion du stock  */

                foreach ($session->get('panier') as $key=>$value){

                    $produits = $reoprodct->find($key);

                    $this->updateStock($produits,$value);

                }
                /* fin de gestion du stock */

                unset($_SESSION['panier']);

                $session->clear();

                $session->remove('panier');

                //$this->addFlash('success','un email vous a été envoyé pour voir la facture !');

                $response = $this->forward('App\Controller\CardController::modal', []);

                return $response;


            } else {

                $this->addFlash("alert alert-danger", "le numero saisi est incorrect");

                return $this->redirectToRoute('commande');

                throw new Exception("le numero saisi est incorrect");
            }
        }
        else {
            $this->addFlash("alert alert-danger", "La carte n'est plus valable");

            return $this->redirectToRoute('commande');

            throw new Exception("La carte n'est plus valable");
        }
    }

    private function updateStock( Product $product, int $quantity)
    {
        try {

            $valeurStock = $product->getStock() - $quantity;

            if($valeurStock < 0){

              $this->addFlash('success','Vous commandez trop de produits il y en a pas assez en stock');

              return $this->redirectToRoute('commande');
            }
            else{
                $product->setStock($valeurStock);
            }

            $this->em->persist($product);

            $this->em->flush();

        } catch (\Exception $e) {

            throw $e;
        }
    }

    private function verifie_num_cc($number, $mod5 = false)
    {

        $parity = strlen($number) % 2;

        $digits = str_split($number);

        $total = 0;

        foreach ($digits as $key => $digit) {

            if (($key % 2) == $parity) {

                $digit *= 2;

                $digit = ($digit >= 10) ? array_sum(str_split($digit)) : $digit;
            }

            $total += $digit;
        }

        return ($total % ($mod5 ? 5 : 10) == 0);
    }

    private function verif_date_exp($mois, $annee): bool
    {
        // Valeur de minuit pour le jour suivant le mois d'expiration */

//        $expiration = mktime(0, 0, 0, $mois + 1, 1, $annee);
//
//        $maintenant = time();
//
//        /* On ne tient pas compte des dates dans plus de 10 ans. */
//
//        $max = $maintenant + (10 * 365 * 24 * 60 * 60);
//
//        if ($expiration > $maintenant && $expiration < $max) {
//            return true;
//        }
//        else
//            {
//            return false;
//        }

        $expiration = mktime(0, 0, 0, $mois + 1, 1, $annee);

        $maintenant = time();

        $max = $maintenant + (10 * 365 * 24 * 60 * 60);

        return ($expiration > $maintenant && $expiration < $max);
    }
     /**
     * @Route("/modal", name="unmodal")
     */
     public function modal(): Response
        {
            return $this->render('modal.html.twig', [
            ]);
        }
}