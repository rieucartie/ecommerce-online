<?php
namespace App\Controller;

use App\Entity\UtilisateurAdresse;
use App\Form\AdresseType;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use App\Repository\TvaRepository;
use App\Repository\UtilisateurAdresseRepository;
use App\Service\Cart\CartService;
use App\Service\GetReferenceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{

    private CartService $CartService;
    private TvaRepository $repoTva;
    private GetReferenceService $referenceService;
    private RequestStack  $requestStack;
    private EntityManagerInterface $entityManager;

    public function __construct(
                                CartService $CartService,
                                TvaRepository $repoTva,
                                GetReferenceService $referenceService,
                                RequestStack  $requestStack,
                                EntityManagerInterface $entityManager
    )
    {
        $this->CartService=$CartService;
        $this->repoTva=$repoTva;
        $this->referenceService=$referenceService;
        $this->requestStack=$requestStack;
        $this->entityManager=$entityManager;

    }

    /**
     * @Route("adresses/nouveau", name="nouvelleadresses")
     * @param UtilisateurAdresseRepository $adresse
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function nouvelleAdresse(UtilisateurAdresseRepository $adresse,
                                    Request $request): RedirectResponse|Response
    {
        $UtilisateurAdresse= new UtilisateurAdresse();

        $form = $this->createForm(AdresseType::class, $UtilisateurAdresse);

        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            /*  var User $user */
            $UtilisateurAdresse->setUser($user);

            $this->entityManager->persist($UtilisateurAdresse);

            $this->entityManager->flush();

            $session = $this->requestStack->getSession();

            $session->set('adresse',$UtilisateurAdresse);

            return $this->redirectToRoute('adresse', array('id' => $user->getId()));

        }
        return $this->render('adresse/new.html.twig', [

            'adresse' => $UtilisateurAdresse,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("adresse", name="adresse")
     * @param UtilisateurAdresseRepository $adresse
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function index(UtilisateurAdresseRepository $adresse,
                                               Request $request): RedirectResponse|Response
    {
         $id=$request->request->get('adressHidden');

         $UserAdresse= new UtilisateurAdresse();

         $form = $this->createForm(AdresseType::class, $UserAdresse);

         $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($UserAdresse);

            $this->entityManager->flush();

            return $this->redirectToRoute('commande');
        }

        if(!empty($this->getUser())){
            $user = $this->getUser()->getId();
        }

        else{
            return $this->redirectToRoute('securitylogin');
        }

        return $this->render('adresse/adresse.html.twig', [
            'adresse' => $adresse->findAdresse($id),
            'user'=> $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("adresses/validation", name="validateadresses")
     * @param CommandeRepository $repo
     * @param EntityManagerInterface $em
     * @param UtilisateurAdresseRepository $adresses
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function validateAdresse(
                                    ProductRepository $repo,
                                    EntityManagerInterface $em,
                                    UtilisateurAdresseRepository
                                    $adresses,Request $request
    ): RedirectResponse|Response
    {
        $session = $this->requestStack->getSession();

       /* id de l'utilisateur adresse */
        $ladress= $request->request->get('adressHide');

        /*  session  */
       $panier=$session->get('panier',[]);

       $commande = $session->get('commande',[]);

       $adresse = $session->get('adresse',[]);


       $adresse= $request->request->get('livraison');


       $adresse = $request->request->get('facturation');


       $session->set('adresse', $adresse);

        /*  commande */

        $tabProduct=[];

        foreach($panier as $id => $quantity){
            $tabProduct[]=[
                'quantity' => $quantity,
                'prix'=> $repo->find($id)
            ];
        }

        // On cherche la livraison avec le id + user + product

        $choixLivraison = $adresses->find($request->request->get('livraison'));

        $choixfacturation = $adresses->find($request->request->get('facturation'));

        [
            "totalHT"=>$totalHT,
            "prixTVA"=>$totalTVA,
            "totalPrixTTC"=>$totalPrixTTC
        ]=$this->TotalPrix();

        return $this->render('commande/index.html.twig', [
            'panier'=>$tabProduct,
            'adresse'=>$adresse,
            'choixLivraison'=>$choixLivraison,
            'choixfacturation'=>$choixfacturation,
            'ladress'=>$ladress,
            '$totalPrixTTC'=>$totalPrixTTC,
            'prixTVA'=>$totalTVA,
            'totalPrixTTC'=>$totalPrixTTC,

        ]);
    }

    private function TotalPrix(){

        $tableauDeProduit = $this->referenceService->destructurationDesProduits();

        $totalHT = 0; $totalTVA = 0;

        foreach ($tableauDeProduit as $produit) {
            $tva = $this->repoTva->find($produit['tva'])->getValeur();
            $totalHT += $produit['price'] * $produit['qte'];
            $totalTVA += ($produit['price'] * $produit['qte'] * $tva) / 100;
        }

        $macommande = [
            "totalHT" => $totalHT,
            "prixTVA" => $totalTVA,
            "totalPrixTTC" => $totalHT + $totalTVA
        ];

        return $macommande;
    }

    /**
     * @Route("quantite/{id}", name="quantite")
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function PasseLaQuantiteAuProduitChoisi(
       $id,
       Request $request
    )
    {
        $session = $this->requestStack->getSession();

        $panier=$session->get('panier');

        if ($request->request->get('qte') != null) {

            $panier[$id]   = $request->request->get('qte');

        }

          $session->set('panier',$panier);

          return $this->redirectToRoute('cartindex');
    }
}
