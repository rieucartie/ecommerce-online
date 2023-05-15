<?php

namespace App\Controller;


use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminAnswerController extends AbstractController
{
    #[Route('/admin/answer', name: 'answer')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(AnswerRepository $answerRepo, Request $request): Response
    {
        $data = new Answer();

        $data->page = $request->get('page', 1);

        $response_filter= $request->get('reponsevide');

        $answer = $answerRepo->findSearch($data,$response_filter);

        return $this->render('admin/answer/index.html.twig', [

            'question' => $answerRepo->findAll(),

            'answers' => $answer,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/answer/{id}/modifier", name="answer_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Answer $answer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request,
                         Answer $answer,
    EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnswerType::class, $answer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('answer');

        }
        return $this->render('admin/answer/edit.html.twig', [

            'answer' => $answer,

            'form' => $form->createView(),
        ]);
    }
}
