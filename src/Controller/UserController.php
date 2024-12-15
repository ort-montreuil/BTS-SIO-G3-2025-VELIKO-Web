<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user)
        {
            if ($user->isBooleanChangerMdp())
            {
                return $this->redirectToRoute("app_change_mdp_force");
            }
        }
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // Redirigez vers la page de connexion
        }

        return $this->render('user/profile.html.twig', [
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
            return $this->redirectToRoute('app_home'); // Redirigez vers la page d'acceuil
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
            'UserType' => $form
        ]);
    }

    #[Route('/user/editPassword/{id}', name: "app_userP")]  //pour éditer les informations de mot de passe d'un utilisateur spécifique
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
    //confirmation supprimer compte
    #[Route('/user/deleteConfirmation/{id}', name: 'user.deleteConfirmation')]
    public function deleteConfirmation(User $user): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur connecté est le même que celui à supprimer
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/deleteConfirmation.html.twig', [
            'user' => $user,
        ]);
    }



    //supprimer compte
    #[Route('/user/deleteAccount/{id}', name: 'user.delete' ,methods: 'POST')]
    public function deleteAccount(User $user, EntityManagerInterface $manager , Request $request): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur connecté est le même que celui à supprimer
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home');
        }

        //verifier token
        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $randomNumber = random_int(0, 99999);
        $randomLettre = chr(random_int(97, 122));
        $randomMdp = str_shuffle($randomLettre . $randomNumber);

        $user->setEmail("anonymous" . $user->getId() . "@veliko.lan");
        $user->setNom("anonymous");
        $user->setPrenom("anonymous");
        $user->setAdresse("anonymous");
        $user->setPassword(password_hash($randomMdp, PASSWORD_BCRYPT));

        $manager->flush();

        // Ajouter un message flash pour informer de la suppression réussie
        $this->addFlash('success', 'Votre compte a été supprimé avec succès.');



        // Rediriger vers la page de connexion
        return $this->redirectToRoute('app_logout');
    }

}