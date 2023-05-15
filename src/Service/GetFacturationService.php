<?php

namespace App\Service;


use App\Repository\CommandeRepository;
use App\Repository\TvaRepository;
use App\Service\Cart\CartService;

class GetFacturationService
{
    private CartService $cartService;
    private TvaRepository $repotva;
    private CommandeRepository $repoCommande;
    private GetReferenceService $referenceService;

    /**
     * GetFacturationService constructor.
     * @param CommandeRepository $repoCommande
     * @param CartService $cartService
     * @param TvaRepository $repotva
     */
    public function __construct(CommandeRepository $repoCommande, CartService $cartService, TvaRepository $repotva,GetReferenceService $referenceService)
    {
        $this->cartService = $cartService;
        $this->repotva = $repotva;
        $this->repoCommande = $repoCommande;
        $this->referenceService = $referenceService;
    }

    public function trouve()
    {

        $tableauDeProduit = $this->referenceService->destructurationDesProduits();
        $macommande = [
            "totalHT" => 0,
            "prixTVA" => 0,
            "totalPrixTTC" => 0
        ];

        foreach ($tableauDeProduit as $produit) {
            $calculTva = $this->repotva->find($produit['tva']);
            $tva = $calculTva->getValeur();

            $prixHT = $produit['price'] * $produit['qte'];
            $macommande['totalHT'] += $prixHT;
            $macommande['prixTVA'] += ($prixHT * $tva) / 100;
        }

        $macommande['totalPrixTTC'] = $macommande['totalHT'] + $macommande['prixTVA'];

        return $macommande;
    }

    public function orderFacture($id)
    {
        $items = $this->repoCommande->trouveCommande($id);

        $macommande = array();
        $totalHT = 0;
        $totalTVA = 0;

        foreach ($items as $produit) {
            foreach ($produit->getLines() as $orderline) {
                //calcul Tva bdd
                $calculTva = $this->repotva->find($orderline->getProduct()->getTva());
                $tva = $calculTva->getValeur();
                $prixHT = ($orderline->getProduct()->getPrice() * $orderline->getQuantity());
                $totalHT += $prixHT;

                $prixTVA = (
                    (($orderline->getProduct()->getPrice() * $orderline->getQuantity()) * $tva) / 100
                );
                $totalTVA += $prixTVA;
                $totalPrixTTC = $totalHT + $totalTVA;

                $macommande = [
                    "totalHT" => $totalHT,
                    "prixTVA" => $totalTVA,
                    "totalPrixTTC" => $totalPrixTTC
                ];
            }
        }

        return $macommande;
    }
}