<?php

namespace App\Controller;
use App\Entity\Tva;
use App\Form\TvaType;
use App\Repository\TvaRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/tva")
 */
class AdminTvaController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{page<\d+>?1}", name="tva_index", methods={"GET"})
     * @param TvaRepository $tvaRepository
     * @param $page
     * @param PaginationService $pagination
     * @return Response
     */
    public function index(
                          TvaRepository $tvaRepository,
                          $page,
                          PaginationService $pagination): Response
    {
        $pagination->setEntityclass(Tva::class)->setLimit(3)->setPage($page);

        return $this->render('admin/tva/index.html.twig', [

                    'tvas' => $tvaRepository->findAll(),
                    'pagination'=>$pagination,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/nouveau", name="tva_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $tva = new Tva();

        $form = $this->createForm(TvaType::class, $tva);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($tva);
            $this->entityManager->flush();
            return $this->redirectToRoute('tva_index');
        }
        return $this->render('admin/tva/new.html.twig', [
                    'tva' => $tva,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/modifier", name="tva_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Tva $tva
     * @return Response
     */
    public function edit(Request $request, Tva $tva): Response
    {
        $form = $this->createForm(TvaType::class, $tva);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();
            return $this->redirectToRoute('tva_index');
        }
        return $this->render('admin/tva/edit.html.twig', [
            'tva' => $tva,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/supprimer/{id}", name="tva_delete", methods={"DELETE"})
     * @param Request $request
     * @param Tva $tva
     * @return Response
     */
    public function delete(Request $request, Tva $tva): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tva->getId(), $request->request->get('_token'))) {

            $this->entityManager->remove($tva);

            $this->entityManager->flush();
        }
        return $this->redirectToRoute('tva_index');
    }
}
