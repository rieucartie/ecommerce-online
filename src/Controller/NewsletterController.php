<?php

namespace App\Controller;


use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
class NewsletterController extends AbstractController
{
    private RequestStack $request;

    /**
     * NewsletterController constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function newsletter(NewsletterRepository $newsletterRepo)
    {
        if($this->getUser()){

                $usermail =$this->getUser()->getEmail();

                $nbuseremail= $newsletterRepo->findInArray($usermail);
        }

        $session =$this->request->getSession();

        $nbViews = $session->get('nbUserViewPage');

       $form = $this->createForm(NewsletterType::class);

        # Affichage du Formulaire Newsletter

        return $this->render('newsletter/subscribe.html.twig', [

            'form' => $form->createView(),

            "nbuseremail"=>!empty($nbuseremail) ? $nbuseremail : null,

            'nbViews' => $nbViews

        ]);
    }

    /**
     * @Route("/signeNewsletter", name="Newsletter_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function new(Request $request,EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        $Newsletter = new Newsletter();

        $form = $this->createForm(NewsletterType::class, $Newsletter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($Newsletter);

            $entityManager->flush();

            return $this->redirectToRoute('products');

        }

        return $this->render('newsletter/subscribe.html.twig', [
            'Newsletter' => $Newsletter,
            'form' => $form->createView(),
        ]);
    }

}