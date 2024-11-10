<?php

namespace App\Controller;

use App\Entity\User;
use App\Controller\EnvoyerMailController;
use App\Form\RegistrationFormType;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
    /**
     * @throws RandomException
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, EnvoyerMailController $mail, TokenService $tokenService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            //sauvegarde user dans bdd
            $entityManager->persist($user);
            $entityManager->flush();

            //activation Token
            $activationToken = $tokenService->generate();
            $user->setToken($activationToken);

            $entityManager->persist($user);
            $entityManager->flush();

            //on envoie un mail
            $mail->send(
                'no-reply@veliko.lan',
                $user->getEmail(),
                'Activation de votre compte',
                'emails',
                ['user' => $user, 'token' => $activationToken]
            );
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,

        ]);
    }


    #[Route('/active/{token}', name: 'app_active')]
    public function activerCompte(string $token, EntityManagerInterface $entityManager): Response

    {
        // Recherche de l'utilisateur avec le token fourni
        $user = $entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Ce token est invalide.');
        }

        // Vérification de l'utilisateur
        $user->setVerified(true);
        $entityManager->flush();

        // Message de succès et redirection
        $this->addFlash('success', 'Votre compte a été activé avec succès.');

        return $this->redirectToRoute('app_login');
    }

}

