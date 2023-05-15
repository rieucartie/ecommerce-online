<?php
namespace App\Service;
use App\Service\Cart\CartService;
use Symfony\Bundle\SecurityBundle\Security;
use  App\Repository\CommandeRepository;

class GetReferenceService
{
    private CommandeRepository $repoComamnde ;

    private CartService $cartService;

    private Security $security;

    public function __construct( Security $security ,CommandeRepository $repoComamnde, CartService $cartService)
    {
        $this->security = $security;
        $this->repoComamnde = $repoComamnde;
        $this->cartService = $cartService;
    }


    public function reference()
    {

        $user = $this->security->getUser();

        //je recupere la derniere commande enregsitré

        $numcom=$this->repoComamnde->findLastOrder($user);

        //je recupere les produits

        $allCommandeDependances = $this->repoComamnde->trouveCommande($numcom);

        return $allCommandeDependances;

    }
    public function referenceByCommande($numcom)
    {
        $user =$this->security->getUser();

        //je recupere les produits

        $allCommandeDependances = $this->repoComamnde->trouveCommande($numcom);

        return $allCommandeDependances;
    }
    public function destructurationDesProduits(){

        $items = $this->cartService->getFullCart();

        $tableauDeProduit = [];

        foreach ($items as $item) {
            $tableauDeProduit[] = [
                'price' => $item['products']->getPrice(),
                'name' => $item['products']->getName(),
                'description' => $item['products']->getDescription(),
                'content' => $item['products']->getContent(),
                'tva' => $item['products']->getTva()->getId(),
                'id' => $item['products']->getId(),
                'fileName' => $item['products']->getfileName(),
                'promo' => $item['products']->getPromo(),
                'categories' => $item['products']->getCategories(),
                'qte' => $item['quantity'],
            ];
        }

        return $tableauDeProduit;
    }
}