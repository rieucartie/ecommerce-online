<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\UserRepository;
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
class AdminController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/utilisateurs", name="utilisateurs")
     * @param UserRepository $user
     * @return Response
     */
    public function usersList(UserRepository $user): Response
    {

        return $this->render("admin/users.html.twig", [
            'users' => $user->findAll()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/utilisateurs/modifier/{id}", name="modifierutilisateur")
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function editUser(Request $request, User $user, EntityManagerInterface $em): RedirectResponse|Response
    {

        $form = $this->createForm(EditUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            return $this->redirectToRoute('admin_utilisateurs');

        }

        return $this->render('admin/editUser.html.twig', ['formUser' => $form->createView()]);
    }
}
