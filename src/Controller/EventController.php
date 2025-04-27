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
use Eluceo\iCal\Domain\Entity\Event as ICalEvent;
use Eluceo\iCal\Domain\Entity\Calendar as ICalCalendar;
use Eluceo\iCal\Domain\ValueObject\DateTime as ICalDateTime;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Eluceo\iCal\Domain\ValueObject\Occurrence;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;




final class EventController extends AbstractController
{
    #[Route('/event/{id_categorie}/event', name:'eventFront')]
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
    
        // Récupérer tous les événements de la catégorie
        $events = $eventRepository->findBy(['categorie' => $categorie]);
        
        // Séparer les événements virtuels et physiques
        $virtualEvents = [];
        $physicalEvents = [];
        
        foreach ($events as $event) {
            if ($event->getType() === 'virtual') {
                $virtualEvents[] = $event;
            } else {
                $physicalEvents[] = $event;
            }
        }
    
        // Créer les formulaires de notation
        $ratingForms = [];
        foreach ($events as $event) {
            $rating = new Rating();
            $rating->setEvent($event);
            $form = $this->createForm(RatingType::class, $rating, [
                'action' => $this->generateUrl('rating_new', ['id_event' => $event->getId()])
            ]);
            $ratingForms[$event->getId()] = $form->createView();
        }
    
        return $this->render('event/eventFront.html.twig', [
            'virtualEvents' => $virtualEvents,
            'physicalEvents' => $physicalEvents,
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

    #[Route('/new/{id_categorie}', name: 'event_new_choice')]
    public function selectType(int $id_categorie, EntityManagerInterface $entityManager): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id_categorie);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        return $this->render('event/select_type.html.twig', [
            'id_categorie' => $id_categorie,
            'categorie' => $categorie
        ]);
    }

    #[Route('/event/{id_categorie}/new', name: 'event_new', methods: ['GET', 'POST'])]
    public function newEvent(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        int $id_categorie
    ): Response {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id_categorie);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $event = new Event();
        $event->setCategorie($categorie);
        // Récupérer le type depuis l'URL ou par défaut 'virtual'
        $type = $request->query->get('type', 'virtual');
        $event->setType($type);
        if ($type === 'virtual') {
            $event->setChannel('linkup');
        }
        $form = $this->createForm(EventType::class, $event, ['type' => $type]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($event->getType() === 'virtual') {
                $event->setAppId('788fe3b38e104edba009da276cb137ca');
                $event->setToken('007eJxTYIjqX6/y6ubjsMgH6VorFkt2ztZvn5FgqdEmW1PB4V7E2K3AYG5hkZZqnGRskWpoYJKakpRoYGCZkmhkbpacZGhsnpzILsWb0RDIyHDgoiwzIwMEgvhsDDmZedmlBQwMAAsNHd8=');
                $event->setChannel('linkup');
            }

            // Gérer l'upload de l'image
            $fileImg = $form->get('image_path')->getData();
            if ($fileImg) {
                $originalFilename = pathinfo($fileImg->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fileImg->guessExtension();

                try {
                    $fileImg->move($this->getParameter('upload_directory'), $newFilename);
                    $event->setImagePath($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès');
            return $this->redirectToRoute('newEventFront', ['id_categorie' => $categorie->getId()]);
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'categorie' => $categorie,
            'type' => $type,
        ]);
    }

    #[Route('/eventFront', name: 'newEventFront')]
    public function newEventFront(Request $request, EventRepository $eventRepository, CategorieRepository $categorieRepository): Response
    {
        $id_categorie = $request->query->get('id_categorie');
        if (!$id_categorie) {
            throw $this->createNotFoundException("Paramètre id_categorie manquant dans l'URL.");
        }
        $categorie = $categorieRepository->find($id_categorie);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }
        $events = $eventRepository->findBy(['categorie' => $categorie]);
        $virtualEvents = [];
        $physicalEvents = [];
        foreach ($events as $event) {
            if ($event->getType() === 'virtual') {
                $virtualEvents[] = $event;
            } else {
                $physicalEvents[] = $event;
            }
        }
        $ratingForms = [];
        foreach ($events as $event) {
            $rating = new \App\Entity\Rating();
            $rating->setEvent($event);
            $form = $this->createForm(\App\Form\RatingType::class, $rating, [
                'action' => $this->generateUrl('rating_new', ['id_event' => $event->getId()])
            ]);
            $ratingForms[$event->getId()] = $form->createView();
        }
        return $this->render('event/eventFront.html.twig', [
            'virtualEvents' => $virtualEvents,
            'physicalEvents' => $physicalEvents,
            'categorie' => $categorie,
            'ratingForms' => $ratingForms
        ]);
    }


    #[Route('/event/edit/{id}', name: 'event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $fileImg = $form->get('image_path')->getData();

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
                    // Handle exception
                }
            
                $event->setImagePath($newFilename);
            }

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

    // Nouvelles méthodes pour les événements virtuels
    
    #[Route('/event/virtual/new', name: 'virtual_event_new', methods: ['GET', 'POST'])]
    public function newVirtualEvent(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $event = new Event();
        
        // Configuration Agora
        $event->setAppId('788fe3b38e104edba009da276cb137ca');
        $event->setToken('007eJxTYIjqX6/y6ubjsMgH6VorFkt2ztZvn5FgqdEmW1PB4V7E2K3AYG5hkZZqnGRskWpoYJKakpRoYGCZkmhkbpacZGhsnpzILsWb0RDIyHDgoiwzIwMEgvhsDDmZedmlBQwMAAsNHd8=');
        $event->setChannel('linkup');
        
        // Autres configurations
        $event->setType('virtual');
        $event->setCode(substr(md5(uniqid()), 0, 6));
        $event->setAcces('public');
        $event->setDuree('60');
        
        // Créer le formulaire
        $form = $this->createFormBuilder($event)
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'événement',
                'attr' => ['placeholder' => 'Ex: Séance de yoga en ligne']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Décrivez votre événement...']
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // S'assurer que le type est toujours 'virtual'
                $event->setType('virtual');
                
                // Persister l'événement
                $entityManager->persist($event);
                $entityManager->flush();
                
                $this->addFlash('success', 'Événement virtuel créé avec succès!');
                
                return $this->redirectToRoute('virtual_event_show', ['id' => $event->getIdEvent()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de l\'événement: ' . $e->getMessage());
            }
        }

        return $this->render('event/virtual_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event/virtual/{id}', name: 'virtual_event_show', methods: ['GET'])]
    public function showVirtualEvent(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }
        return $this->render('event/virtual_show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/virtual/{id}/join', name: 'virtual_event_join', methods: ['GET'])]
    public function joinVirtualEvent(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }
        if ($event->getType() !== 'virtual') {
            throw $this->createNotFoundException('Cet événement n\'est pas un événement virtuel.');
        }
        return $this->render('event/virtual_join.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/virtual/{id}/end', name: 'virtual_event_end', methods: ['POST'])]
    public function endVirtualEvent(int $id, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }
        if ($event->getType() !== 'virtual') {
            throw $this->createNotFoundException('Cet événement n\'est pas un événement virtuel.');
        }
        $event->setEndDate(new \DateTime());
        $entityManager->flush();
        return $this->redirectToRoute('event_detail', ['id_event' => $id]);
    }

    #[Route('/calendar/ics', name: 'calendar_ics')]
    public function ics(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        $calendar = new ICalCalendar();

        foreach ($events as $event) {
            $icalEvent = new ICalEvent();
            $icalEvent->setSummary($event->getTitre());
            $occurrence = new TimeSpan(
                new ICalDateTime($event->getDateDebut(), false),
                new ICalDateTime($event->getDateFin(), false)
            );
            $icalEvent->setOccurrence($occurrence);
            $calendar->addEvent($icalEvent);
        }

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        return new Response(
            $calendarComponent,
            200,
            [
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="calendar.ics"',
            ]
        );
    }
}
