<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AProposController extends AbstractController
{
    #[Route('/apropos', name: 'app_a_propos')]
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
        return $this->render('a_propos/apropos.html.twig', [
            'controller_name' => 'AProposController',
        ]);
    }
}
