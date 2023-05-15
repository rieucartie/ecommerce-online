<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Line;
use App\Repository\CommandeRepository;
use App\Repository\LineRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin", name="admin_")
 */
class AdminCommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/Commande/{page<\d+>?1}", name="admin_Commande")
     * @param CommandeRepository $repoCommande
     * @param LineRepository $lineRepository
     * @param $page
     * @param PaginationService $pagination
     * @return Response
     */
    public function Commande(
        CommandeRepository $repoCommande,
        LineRepository $lineRepository,
        $page,
        PaginationService $pagination): Response
    {

        $pagination->setEntityclass(Commande::class)->setLimit(3)->setPage($page);

        return $this->render("admin/commande.html.twig", [

            'lines' => $lineRepository->findAll(),

            'orders' => $repoCommande->findAll(),

            'pagination' => $pagination,

            'page' => $page

        ]);
    }

    /**
     * @param Commande $order
     * @return RedirectResponse
     * @Route("/{id}/cancel/{page<\d+>?1}", name="ordercancel")
     * @IsGranted("ROLE_ADMIN")
     */
    public function cancel(Commande $order, $page): RedirectResponse
    {
        $order->setState('canceled');

        $order->setCanceledAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $this->redirectToRoute("admin_admin_Commande", ["page" => $page]);
    }

    /**
     * @param Commande $order
     * @return RedirectResponse
     * @Route("/{id}/validerDateCommande/{page<\d+>?1}", name="validerDateCommande")
     * @IsGranted("ROLE_ADMIN")
     */
    public function validerDateCommande(Commande $order, $page): RedirectResponse
    {
        $order->setState('treated');

        $order->setUpdateAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $this->redirectToRoute("admin_admin_Commande", ["page" => $page]);
    }

    /**
     * @Route("/{id}/facture", name="VoirFacture")
     * @IsGranted("ROLE_ADMIN")
     * @param $id
     * @param CommandeRepository $repoCommande
     * @return Response
     */
    public function VoirFacture($id, CommandeRepository $repoCommande): Response
    {

        return $this->render("admin/facture/FactureList.html.twig", [
            'orders' => $repoCommande->searchUserAdress($id),
        ]);

    }
}
