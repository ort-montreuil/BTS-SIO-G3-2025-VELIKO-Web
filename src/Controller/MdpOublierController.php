<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MdpOublierFormType;
use App\Form\ResetMdpFormType;
use App\Service\TokenService;
use App\Controller\EnvoyerMailController;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MdpOublierController extends AbstractController
{

    #[Route('/mdpOublier', name: 'app_mdpOublier')]
    public function mdpOublier(Request $request, EntityManagerInterface $entityManager, TokenService $tokenService, EnvoyerMailController $mail): Response
    {
        $user = $this->getUser();
        if ($user)
        {
            if ($user->isBooleanChangerMdp())
            {
                return $this->redirectToRoute("app_change_mdp_force");
            }
        }

        $form = $this->createForm(MdpOublierFormType::class); //creer le formulaire
        $form->handleRequest($request); //gere la requete


        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);


            //sauvegarder user dans bdd
            $entityManager->persist($user);
            $entityManager->flush();


            if ($user) {
                //activation Token
                $activationToken = $tokenService->generate();
                $user->setToken($activationToken);

                $entityManager->persist($user);
                $entityManager->flush();

                //on envoie un mail
                $mail->send(
                    'no-reply@veliko.lan',
                    $user->getEmail(),
                    'Reinitialisation de votre mot de passe',
                    'emailMdp',
                    ['user' => $user, 'token' => $activationToken]
                );
            }
        }

        return $this->render('mdpOublier/mdpOublier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/resetMdp/{token}', name: 'app_resetMdp')]
    public function resetMdp(string $token, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy((['token' => $token]));

        if (!$user) {
            throw $this->createNotFoundException('Ce token est invalide.');
        }

        $form = $this->createForm((ResetMdpFormType::class));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user,$newPassword));


            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été modifié');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('mdpOublier/resetMdp.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
