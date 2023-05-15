<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\Cart\CartService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/product")
 */
class AdminProductController extends AbstractController
{

    public const MAJQTE = "la quantité a bien été mise à jour";

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/stockupdate/{page<\d+>?1}", name="product_stock_update")
     * @param ProductRepository $productRepository
     * @param $page
     * @param PaginationService $pagination
     * @param CartService $CartService
     * @return Response
     */
    public function updateTheStock(
        ProductRepository $productRepository,
        $page,
        PaginationService $pagination,
        CartService $CartService): Response
    {

        $pagination->setEntityclass(Product::class)->setLimit(10)->setPage($page);

        return $this->render('admin/product/stocks.html.twig', [

            'products' => $productRepository->findAll(),
            'pagination' => $pagination,
            'items' => $CartService->getFullCart(),

        ]);

    }

    /**
     * @Route("MAJquantite/{id}", name="Updatequantite")
     * @param ProductRepository $productRepository
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function PasseLaQuantiteAuProduitChoisi(
        ProductRepository $productRepository,
        $id,
        Request $request): RedirectResponse
    {

        $qte = $request->request->get('qte');

        if ($qte != null) {

            $productRepository->majQte($qte, $id);

            $this->addFlash('success', self::MAJQTE);

        }
        return $this->redirectToRoute('product_stock_update');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{page<\d+>?1}", name="product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @param $page
     * @param PaginationService $pagination
     * @return Response
     */
    public function index(
        ProductRepository $productRepository,
        $page,
        PaginationService $pagination): Response
    {

        $pagination->setEntityclass(Product::class)->setLimit(10)->setPage($page);

        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/nouveau", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setImageFile($product->getImageFile());

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('product_index');
        }
        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'productName' => $product->getImageFile()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/modifier", name="product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function edit(
        Request $request,
        Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productName = "";

            if (file_exists($product->getImageFile())) {

                $product->setImageFile($product->getImageFile());
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('product_index');
        }
        $productName = isset ($productName) ? $product->getFileName() : null;

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'productName' => isset ($product) ? $product->getFileName() : null,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/supprimer/{id}", name="product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request,
                           Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {

            $this->entityManager->remove($product);

            $directoryPath = $product->getFileName();

            $filesystem = new Filesystem();

            $filesystem->remove($directoryPath);

            $this->entityManager->flush();
        }
        return $this->redirectToRoute('product_index');
    }


}
