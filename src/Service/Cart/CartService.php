<?php

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{

    protected RequestStack $requestStack;

    public function __construct(ProductRepository $product, RequestStack $requestStack)
    {
        $this->product = $product;
        $this->requestStack = $requestStack;
    }

    public function add(int $id)
    {
        $panier = $this->requestStack->getSession()->get('panier', []);

        $panier[$id] = ($panier[$id] ?? 0) + (float) $this->requestStack->getCurrentRequest()->get('qte', 1);

        $this->requestStack->getSession()->set('panier', $panier);
    }

    public function remove(int $id)
    {

        $this->requestStack->getSession()->set('panier', array_diff_key($this->requestStack->getSession()->get('panier', []), [$id => true]));

    }

    public function getTotal(): float
    {
        $total = array_reduce($this->getFullCart(), function ($carry, $item) {

            $tva = $item['products']->getPrice() * (1 + ($item['products']->getTva()->getValeur() / 100));

            return $carry + ($tva * $item['quantity']);

        }, 0);

        return $total;
    }

    public function getFullCart(): array
    {
        return array_map(function ($id, $quantity) {
            return [
                'products' => $this->product->find($id),
                'quantity' => $quantity
            ];
        }, array_keys($this->requestStack->getSession()->get('panier', [])), $this->requestStack->getSession()->get('panier', []));

    }
}
