<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Line;
use App\Entity\Product;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use App\Repository\TvaRepository;
use App\Repository\UtilisateurAdresseRepository;
use App\Service\Cart\CartService;
use App\Service\GetFacturationService;
use App\Service\GetReferenceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use function Couchbase\defaultDecoder;

class CommandeController extends AbstractController
{
    private GetFacturationService $facturationService;
    private GetReferenceService $ReferenceService;
    private EntityManagerInterface $entityManager;
    private RequestStack $request;


    public function __construct(GetReferenceService $ReferenceService,
                                GetFacturationService $facturationService,
                                EntityManagerInterface $entityManager,
                                RequestStack $request)
    {
        $this->facturationService = $facturationService;
        $this->ReferenceService = $ReferenceService;
        $this->entityManager=$entityManager;
        $this->request=$request;
    }
    /**
     * @Route("/commande", name="commande")
     * @param Request $request
     * @param CartService $CartService
     * @param UtilisateurAdresseRepository $repoadress
     * @param CommandeRepository $repocommande
     * @param ProductRepository $repoProduct
     * @param TvaRepository $repoTva
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request,
                          CartService $CartService,
                          UtilisateurAdresseRepository $repoadress,
                          CommandeRepository $repocommande,
                          ProductRepository $repoProduct,
                          TvaRepository $repoTva): Response
    {
        $session = $this->request->getSession();

        // je dois retourner la nouvelle adresse

        $adresse = $session->get('adresse');

        $adress = $request->request->get('ladress');

        $ajoutUser = $this->getUser();

        $items = $CartService->getFullCart();

        $tableauDeProduit =$this->ReferenceService->destructurationDesProduits();

        //compte nombre commande par session

       /* if ($repocommande->CommandExists($user) >= 1) {

        }

        else {*/

            /*  ajout de la commande  */

            $commande = new Commande();

            $idcom = $session->get('idcom');

            $commande->setValider(0);

            $commande->setUser($ajoutUser);

            $this->entityManager->persist($commande);

            $this->entityManager->flush();

            $lastinsertid = $commande->getId();

            //on va creer une session commande

            $idcom = $session->set('idcom', $lastinsertid);

            /* on récupére les produits dans le panier par leur id  */

            $tabProducts["produit"] = [];

            foreach ($tableauDeProduit as $IdProduct) {
                $tabProducts[]= $repoProduct->findProducts($IdProduct['id']);
            }

            $tabProductsQuantity = [];

            foreach ($tableauDeProduit as $ProductQte) {
                $id = $ProductQte['id'];
                $tabProductsQuantity[$id] = $ProductQte['qte'];
            }

            /* on parcours les produits dans le panier par leur id  */

            foreach ($tabProductsQuantity as $key=>$value)
                    {
                        $line = new Line();
                        $line->setQuantity($value);
                        $line->setOrder($commande);

                        $line->setAmount($repoProduct->find($key)->getPrice());

                        $line->setProduct($repoProduct->find($key));
                        $this->entityManager->persist($line);
                    }

        $this->entityManager->flush();

        return $this->render('validation/validation.html.twig', array(
            'tabs' => $tableauDeProduit,
            'commande' => $this->facturationService->trouve(),
            'order' =>$commande
        ));
    }


    /**
     * @param Request $request
     * @param CommandeRepository $orderRepository
     * @return Response
     * @Route("/history", name="order_history")
     * @IsGranted("ROLE_USER")
     */
    public function history(Request $request,CommandeRepository $orderRepository): Response
    {
        // 1) Je cherche les résultats des commandes totales

        $resultats = $orderRepository->findalls();

        // 2) Je cherche à connaître le retour de la liste déroulante grâce à l'ID sélectionné dans la liste

            $envoieDuStatut = $request->request->get('suividecommande');

            $reqCommande = $orderRepository->trouveCommandeHistorique($envoieDuStatut);

            $orders=$orderRepository->byHistorique($this->getUser()->getId(), $envoieDuStatut);

        // 3) je trouve les produits et le user correspondant a la commande n° de l'id sélectionné dans la liste

        return $this->render("commande/historyUser.html.twig", [
            "orders" => $orders ? $orders : null,
            'reqCommande' => $reqCommande ,// $reqCommande= liste de ligne de commande de product
            'resultats' => $resultats ,
            'envoieDuStatut'=>$envoieDuStatut
        ]);
    }

}
