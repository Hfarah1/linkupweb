<?php

namespace App\Controller;
use App\Entity\Categorie;
use App\Entity\Event;
use App\Form\CategorieType;
use App\Form\EventType;
use App\Form\RatingType;
use App\Entity\Rating;
use App\Repository\CategorieRepository;
use App\Repository\RatingRepository;
use App\Repository\EventRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;




final class EventController extends AbstractController
{
    #[Route('/event/{id_categorie}/event', name:'newEventFront')]
    public function index(
        Request $request,
        EventRepository $eventRepository, 
        CategorieRepository $categorieRepository,
        int $id_categorie
    ): Response {
        $categorie = $categorieRepository->find($id_categorie);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }
    
        $events = $eventRepository->findBy(['categorie' => $categorie]);
    
        $ratingForms = [];
        foreach ($events as $event) {
            $rating = new Rating();
            $rating->setEvent($event);
            $form = $this->createForm(RatingType::class, $rating, [
                'action' => $this->generateUrl('rating_new', ['id_event' => $event->getId_event()])
            ]);
            $ratingForms[$event->getId_event()] = $form->createView();
        }
    
        return $this->render('event/eventFront.html.twig', [
            'events' => $events,
            'categorie' => $categorie,
            'ratingForms' => $ratingForms
        ]);
    }
    

    #[Route('/eventslist', name: 'eventslist')]
    public function eventslist(EventRepository $EventRepository): Response
    {
        $events = $EventRepository->findAll();
        return $this->render('event/eventsback.html.twig', [
            'events' => $events,
        ]);
    }
    #[Route('/eventRating/{id_event}', name: 'event_detail', methods: ['GET'])]
public function eventDetail(
    int $id_event,
    EventRepository $eventRepository,
    RatingRepository $ratingRepository
): Response {
    $event = $eventRepository->find($id_event);
    if (!$event) {
        throw $this->createNotFoundException('Événement non trouvé.');
    }

    $ratings = $ratingRepository->findBy(['event' => $event]);

    $n = count($ratings);
    $n1 = $n2 = $n3 = $n4 = $n5 = 0;
    $sum = 0;

    foreach ($ratings as $rating) {
        $note = $rating->getNote();
        $sum += $note;
        switch ($note) {
            case 5: $n5++; break;
            case 4: $n4++; break;
            case 3: $n3++; break;
            case 2: $n2++; break;
            case 1: $n1++; break;
        }
    }

    $moyenne = $n > 0 ? round($sum / $n, 2) : 0;

    return $this->render('event/details.html.twig', [
        'event' => $event,
        'ratings' => $ratings,
        'n' => $n,
        'n1' => $n1,
        'n2' => $n2,
        'n3' => $n3,
        'n4' => $n4,
        'n5' => $n5,
        'moyenne' => $moyenne
    ]);
}

    #[Route('/new/{id_categorie}', name: '/new/{id_categorie}', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    EventRepository $eventRepository,
    SluggerInterface $slugger,
    CategorieRepository $categorieRepository,
    int $id_categorie
): Response {
    $categorie = $categorieRepository->find($id_categorie);
    if (!$categorie) {
        throw $this->createNotFoundException('Catégorie non trouvée.');
    }

    $event = new Event();
    $event->setCategorie($categorie);

    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        
        $fileImg = $form->get('imagePath')->getData();
        if ($fileImg) {
            $originalFilename = pathinfo($fileImg->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$fileImg->guessExtension();

            try {
                $fileImg->move($this->getParameter('upload_directory'), $newFilename);
            } catch (FileException $e) {
                // Gérer l'exception (facultatif)
            }

            $event->setImagePath($newFilename);
        }

        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('eventslist');
    }

    return $this->renderForm('/event/new.html.twig', [
        'form' => $form->createView(),
        
        'event' => $event,
    ]);
}

  
    #[Route('/eventFront', name: 'EventFront')]
    public function newEventFront(EventRepository $EventRepository): Response
    {
        $events = $EventRepository->findAll();
        return $this->render('eventfront.html.twig', [
            'events' => $events,
        ]);
    }


    #[Route('/event/edit/{id}', name: 'event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $fileImg = $form->get('imagePath')->getData();

            
            if ($fileImg) {
                $originalFilename = pathinfo($fileImg->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fileImg->guessExtension();
            
                
                try {
                    $fileImg->move(
                        $this->getParameter('upload_directory'), 
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }
            
                
                $event->setImagePath($newFilename);}

            

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

           
            return $this->redirectToRoute('eventslist');
        }

        return $this->renderForm('event/edit.html.twig', [
            'form' => $form->createView(),
           
            'event' => $event,
        ]);
    }


    #[Route('/event/delete/{id}', name: 'event_delete', methods: ['GET', 'POST'])]
    public function deleteEvent($id, ManagerRegistry $managerRegistry, EventRepository $EventRepository): Response
    {
        $entityManager = $managerRegistry->getManager();
        
        // Find the category by ID
        $event = $EventRepository->find($id);
        
        // Check if the category exists
        if ($event) {
            // Remove the produit from the database
            $entityManager->remove($event);
            $entityManager->flush();
        }
        
        // Redirect to the list page of produits after deletion
        return $this->redirectToRoute('eventslist', [], Response::HTTP_SEE_OTHER);
    }
    









}
