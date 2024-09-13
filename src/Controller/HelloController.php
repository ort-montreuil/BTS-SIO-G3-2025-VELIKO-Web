<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{
    # = attributs
    # @= annotations
    #[Route('/hello')]
    public function Hello(): Response
    {

        return $this->render('hello/hello.html.twig', [
        ]);

    }


}