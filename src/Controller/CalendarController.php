<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
class CalendarController extends AbstractController
{
    

    #[Route('/calendar/{categorieId}', name: 'app_calendar')]
    public function index($categorieId, Request $request): Response
    {
        return $this->render('calendar/index.html.twig', [
            'categorieId' => $categorieId,
        ]);
    }

    #[Route('/calendar/{categorieId}/events', name: 'app_calendar_events')]
    public function getEvents($categorieId, EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findBy(['categorie' => $categorieId]);
        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'title' => $event->getTitre(),
                'start' => $event->getDateDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getDateFin()->format('Y-m-d H:i:s'),
                'description' => $event->getDescription(),
                'color' => '#1976d2',
            ];
        }
        return $this->json($data);
    }
} 