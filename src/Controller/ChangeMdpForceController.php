<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ChangeMdpForceController extends AbstractController
{

    #[Route('/change/mdp/force', name: 'app_change_mdp_force')]
    public function forced( Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {

        $user = $this->getUser();
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

                //mettrea jour le mot de passe forcer

                $user->setBooleanChangerMdp(0);
                // Sauvegarder les modifications
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Le mot de passe de votre compte a bien été modifié.');
                return $this->redirectToRoute('app_user'); // Rediriger après la modification
            } else {
                $this->addFlash('warning', 'Le mot de passe actuel est incorrect.');
            }
        }

        return $this->render('change_mdp_force/index.html.twig', [
            'controller_name' => 'ChangeMdpForceController',
            'form' => $form
        ]);
    }
}
