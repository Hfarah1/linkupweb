<?php

namespace App\Controller;
use App\Entity\Rencontre;
use App\Form\RencontreType;
use App\Repository\RencontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class backController extends AbstractController
{
    #[Route('/back', name: 'back')]
    public function index(): Response
    {
        return $this->render('baseback.html.twig', [
            'controller_name' => 'backController',
        ]);
    }
    #[Route('/stats', name: 'app_stats')]
    public function statistiques(RencontreRepository $transprepo)
    {
        $transprepo = $transprepo->findAll();

        $transpId = [];
        
        foreach ($transprepo as $transport_reservations) {
            $transpId[] = $transport_reservations->getCategorieRencontre();
            $occurrences = array_count_values($transpId);
        }

        return $this->render('stats.html.twig', [
            'transpId' => json_encode($transpId),
            'transpIdCount' => json_encode($occurrences),
        ]);
    }
}