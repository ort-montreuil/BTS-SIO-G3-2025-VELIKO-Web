<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/edition/{id}', name: 'user.edit')]  //pour éditer les informations d'un utilisateur spécifique
    public function edit(User $user , Request $request , EntityManagerInterface $manager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // Redirigez vers la page de connexion
        }

        // Vérifier si l'utilisateur connecté est le même que celui à éditer
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home'); // Redirigez vers la page d'accueil
        }

        // Créer le formulaire avec l'utilisateur récupéré
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les infos de votre compte ont bien été modifiées.'
            );
            return  $this->redirectToRoute('app_user');
        }

        // Rendre le formulaire dans la vue
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(), // Passe le formulaire à la vue
        ]);
    }
}
