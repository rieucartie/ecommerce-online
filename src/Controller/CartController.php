<?php

namespace App\Controller;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CartController extends AbstractController {

    /**
     * @Route("/panier", name="cartindex")
     * @param CartService $CartService
     * @return Response
     */
    public function index(CartService $CartService): Response
    {

        $user = !empty($this->getUser()) ? $this->getUser()->getId() : null;

        return $this->render('cart/index.html.twig', [
                    'items' => $CartService->getFullCart(),
                    'total' => $CartService->getTotal(),
                    'user' => isset($user) ? $user : 0
        ]);
    }

    /**
     * @Route("panier/ajouter/{id}", name="cartadd")
     * @param $id
     * @param CartService $CartService
     * @return RedirectResponse
     */
    public function add($id, CartService $CartService): RedirectResponse
    {
        $CartService->add($id);

        return $this->redirectToRoute('cartindex');
    }

    /**
     * @Route("panier/supprimer/{id}", name="cartremove")
     * @param $id
     * @param CartService $CartService
     * @return RedirectResponse
     */
    public function remove($id, CartService $CartService): RedirectResponse
    {
        $CartService->remove($id);

        return $this->redirectToRoute('cartindex');
    }
}
