<?php

namespace App\Controller;
use App\Entity\Answer;
use App\Entity\Product;
use App\Entity\Question;
use App\Entity\User;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Data\SearchData;
use App\Form\SearchForm;
class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     * @param ProductRepository $repository
     * @param Request $request
     * @return Response
     */
    public function index(
        ProductRepository $repository,
        Request $request)
    {

        $data = new SearchData();

        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchForm::class, $data);

        $form->handleRequest($request);

        [$min,$max] = $repository->findMinMax($data);

        $sort = $request->query->get("sort", "new-products");

        $SortProducts= $repository->getSortProducts($sort);

        $products = $repository->findSearch($data);

        return $this->render('products/index.html.twig', [
            'products' => $products,
            'product' => $SortProducts,
            'form' => $form->createView(),
            'min' => $min,
             'max' => $max,
            "params" => [
                "sort" => $sort
            ]
        ]);
    }

    /**
     * @Route("/products/{id}", name="infoproduct")
     * @param QuestionRepository $repoQuestion
     * @param ProductRepository $repository
     * @param $id
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function show(
                         QuestionRepository $repoQuestion,
                         ProductRepository $repository, $id,
                         Request $request,Product $product,
                         EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        $repository->find($id);

        $questionAsked = $repoQuestion->SearchQuestion((int)$id);

        $question = new Question();
        $answer=new Answer();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setProduct($product);

            /* @var $user User */
            $question->setUser($this->getUser());

            $question->setCreated(new \DateTime());

            $entityManager->persist($question);

            $entityManager->flush();

            $answer->setQuestion($question);

            $entityManager->persist($answer);

            $entityManager->flush();

            return $this->redirectToRoute('infoproduct', array(
                'id' => $id,
            ));
        }

        return $this->render('products/uniqueproduct.html.twig', [
            'produit' => $repository->find($id),
            'form' => $form->createView(),
            'questionAsked'=>$questionAsked
        ]);
    }

}
