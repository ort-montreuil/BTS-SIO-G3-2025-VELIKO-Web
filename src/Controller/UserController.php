<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // Redirigez vers la page de connexion
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/edition/{id}', name: 'user.edit')]  //pour éditer les informations d'un utilisateur spécifique
    public function edit(User $user, Request $request, EntityManagerInterface $manager): Response
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
            // Mettre à jour les données de l'utilisateur
            $manager->flush();

            $this->addFlash('success', 'Les infos de votre compte ont bien été modifiées.');
            return $this->redirectToRoute('app_user');
        }

        // Rendre le formulaire dans la vue
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(), // Passe le formulaire à la vue
        ]);
    }

    #[Route('/user/editPassword/{id}', name: 'user.password')]  //pour éditer les informations de mot de passe d'un utilisateur spécifique
    public function editPassword(User $user, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        // Créer le formulaire pour le changement de mot de passe
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request); // Assurez-vous de gérer la requête

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe actuel saisi par l'utilisateur
            $currentPassword = $form->get('password')->getData();

            // Vérifier que le mot de passe actuel est valide
            if ($hasher->isPasswordValid($user, $currentPassword)) {
                // Récupérer le nouveau mot de passe
                $newPassword = $form->get('newPassword')->getData(); // Récupérer le premier champ du mot de passe répété

                // Hachage du nouveau mot de passe et mise à jour de l'utilisateur
                $user->setPassword($hasher->hashPassword($user, $newPassword)); // Hachage du nouveau mot de passe

                // Sauvegarder les modifications
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Le mot de passe de votre compte a bien été modifié.');
                return $this->redirectToRoute('app_user'); // Rediriger après la modification
            } else {
                $this->addFlash('warning', 'Le mot de passe actuel est incorrect.');
            }
        }

        // Rendre le formulaire dans la vue
        return $this->render('user/editPassword.html.twig', [
            'form' => $form->createView(), // Passe le formulaire à la vue
        ]);
    }
}



