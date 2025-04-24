<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\Event;
use App\Form\RatingType;
use App\Form\EventType;
use App\Repository\RatingRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RatingController extends AbstractController
{
    #[Route('/rating/{id_event}', name: 'newRatingFront')]
    public function index(EventRepository $eventRepository, RatingRepository $ratingRepository, int $id_event): Response
    {
        $event = $eventRepository->find($id_event);
        $ratings = $ratingRepository->findBy(['event' => $event]);

        return $this->render('rating/ratingFront.html.twig', [
            'ratings' => $ratings,
            'event' => $event,
        ]);
    }

    #[Route('/ratingslist', name: 'ratingslist')]
    public function ratingslist(RatingRepository $ratingRepository): Response
    {
        $ratings = $ratingRepository->findAll();
        return $this->render('rating/ratingsback.html.twig', [
            'ratings' => $ratings,
        ]);
    }

    #[Route('/newRating/{id_event}', name: 'rating_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        int $id_event
    ): Response {
        $event = $eventRepository->find($id_event);
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé.');
        }
    
        $rating = new Rating();
        $rating->setEvent($event);
    
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rating);
            $entityManager->flush();
    
            $this->addFlash('success', 'Merci pour votre notation !');
        } else {
            $this->addFlash('error', 'La notation n\'a pas pu être enregistrée.');
        }
    
        return $this->redirectToRoute('newEventFront', [
            'id_categorie' => $event->getCategorie()->getId_categorie()
        ]);
    }
    

    #[Route('/rating/edit/{id}', name: 'rating_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rating $rating, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('ratingslist');
        }

        return $this->render('rating/edit.html.twig', [
            'form' => $form->createView(),
            'rating' => $rating,
        ]);
    }

    #[Route('/rating/delete/{id}', name: 'rating_delete', methods: ['POST'])]
    public function delete(Request $request, Rating $rating, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rating->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rating);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ratingslist');
    }
}