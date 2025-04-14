<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackController extends AbstractController
{
    #[Route('/Bback', name: 'back')]
    public function index(): Response
    {
        return $this->render('baseback.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
}
