<?php

namespace App\Controller;
use Twilio\Rest\Client;

use App\Entity\Rencontre;
use App\Form\RencontreType;
use App\Repository\RencontreRepository;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/rencontre')]
final class RencontreController extends AbstractController{
    #[Route(name: 'app_rencontre_index', methods: ['GET'])]
    public function index(RencontreRepository $rencontreRepository): Response
    {
        return $this->render('rencontre/index.html.twig', [
            'rencontres' => $rencontreRepository->findAll(),
        ]);
    }
    #[Route('/participate', name: 'app_participate')]
    public function participate(): Response
    {
        $sid = $_ENV['TWILIO_SID'] ?? getenv('TWILIO_SID');
        $token = $_ENV['TWILIO_TOKEN'] ?? getenv('TWILIO_TOKEN');
        $twilio = new Client($sid, $token);

        try {
            $message = $twilio->messages->create(
                "+21655559608", // <-- Destination phone number
                [
                    "from" => "+16194040495", // <-- Your Twilio number
                    "body" => "Bonjour ! Merci pour votre participation. L'équipe LinkUp vous remercie !"
                ]
            );
            flash()->addSuccess('success', 'SMS envoyé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi du SMS: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_rencontre_index'); // Redirect wherever you want
    }
    #[Route('/new', name: 'app_rencontre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rencontre = new Rencontre();
        // Set default values
        $rencontre->setIdUtilisateur(1); // Default user ID (should be the current user in a real app)
        $rencontre->setStatut('actif');
        $rencontre->setCreated_at(new \DateTime());
        $rencontre->setDate_rencontre(new \DateTime()); // Ensure date is set
        
        $form = $this->createForm(RencontreType::class, $rencontre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Handle image upload if present
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
                    
                    try {
                        $imageFile->move(
                            $this->getParameter('rencontres_directory'),
                            $newFilename
                        );
                        $rencontre->setImage_name($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image: ' . $e->getMessage());
                        return $this->redirectToRoute('app_rencontre_new');
                    }
                }
                
                // Double-check date is set
                if ($rencontre->getDate_rencontre() === null) {
                    $rencontre->setDate_rencontre(new \DateTime());
                }
                
                $entityManager->persist($rencontre);
                $entityManager->flush();

                flash()->addSuccess('success', 'Votre rencontre a été créée avec succès!');
                return $this->redirectToRoute('app_rencontre_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de la rencontre: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
            // Add flash message for form errors
            $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez les corriger.');
            
            // Check specifically for date_rencontre errors
            if ($form->get('date_rencontre')->getErrors()->count() > 0) {
                foreach ($form->get('date_rencontre')->getErrors() as $error) {
                    $this->addFlash('error', 'Date de rencontre: ' . $error->getMessage());
                }
            }
            
            // Log form errors for debugging
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('rencontre/new.html.twig', [
            'rencontre' => $rencontre,
            'form' => $form,
        ]);
    }

    #[Route('/{id_rencontre}', name: 'app_rencontre_show', methods: ['GET'])]
    public function show(Rencontre $rencontre, FeedbackRepository $feedbackRepository): Response
    {
        // Ensure any null DateTime fields are handled properly
        if ($rencontre->getCreatedAt() === null) {
            $rencontre->setCreatedAt(new \DateTime());
        }
        
        // Get feedback for this rencontre with utilisateur data
        $feedbacks = $feedbackRepository->findByRencontreWithUtilisateur($rencontre->getId_rencontre());
        $feedbackCount = $feedbackRepository->countByRencontre($rencontre->getId_rencontre());
        
        // Get replies for each feedback with utilisateur data
        $repliesMap = [];
        foreach ($feedbacks as $feedback) {
            $repliesMap[$feedback->getIdFeedback()] = $feedbackRepository->findRepliesWithUtilisateur($feedback->getIdFeedback());
        }
        
        return $this->render('rencontre/show.html.twig', [
            'rencontre' => $rencontre,
            'feedbacks' => $feedbacks,
            'repliesMap' => $repliesMap,
            'feedbackCount' => $feedbackCount
        ]);
    }

    #[Route('/{id_rencontre}/edit', name: 'app_rencontre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rencontre $rencontre, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Ensure any null DateTime fields are handled properly
        if ($rencontre->getCreatedAt() === null) {
            $rencontre->setCreatedAt(new \DateTime());
        }
        
        // Ensure date_rencontre is set
        if ($rencontre->getDate_rencontre() === null) {
            $rencontre->setDate_rencontre(new \DateTime());
        }
        
        $oldImageName = $rencontre->getImageName();
        $form = $this->createForm(RencontreType::class, $rencontre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Handle image upload
                $imageFile = $form->get('image')->getData();
                
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    
                    try {
                        $imageFile->move(
                            $this->getParameter('rencontres_directory'),
                            $newFilename
                        );
                        
                        // Update the 'image_name' property to store the image file name
                        $rencontre->setImageName($newFilename);
                        
                        // Remove old image if exists
                        if ($oldImageName) {
                            $oldImagePath = $this->getParameter('rencontres_directory') . '/' . $oldImageName;
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    } catch (FileException $e) {
                        // Handle exception if something happens during file upload
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image: ' . $e->getMessage());
                    }
                } else {
                    // If no new image is uploaded, keep the old image name
                    $rencontre->setImageName($oldImageName);
                }
                
                // Double-check date is set
                if ($rencontre->getDate_rencontre() === null) {
                    $rencontre->setDate_rencontre(new \DateTime());
                }
                
                $entityManager->flush();
                
                $this->addFlash('success', 'La rencontre a été modifiée avec succès!');
                return $this->redirectToRoute('app_rencontre_show', ['id_rencontre' => $rencontre->getId_rencontre()], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de la rencontre: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
            // Add flash message for form errors
            $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez les corriger.');
            
            // Check specifically for date_rencontre errors
            if ($form->get('date_rencontre')->getErrors()->count() > 0) {
                foreach ($form->get('date_rencontre')->getErrors() as $error) {
                    $this->addFlash('error', 'Date de rencontre: ' . $error->getMessage());
                }
            }
            
            // Log form errors for debugging
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('rencontre/edit.html.twig', [
            'rencontre' => $rencontre,
            'form' => $form,
        ]);
    }

    #[Route('/{id_rencontre}', name: 'app_rencontre_delete', methods: ['POST'])]
    public function delete(Request $request, Rencontre $rencontre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rencontre->getId_rencontre(), $request->request->get('_token'))) {
            // Remove image file if exists
            $imageName = $rencontre->getImageName();
            if ($imageName) {
                $imagePath = $this->getParameter('rencontres_directory') . '/' . $imageName;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $entityManager->remove($rencontre);
            $entityManager->flush();
            
            $this->addFlash('success', 'La rencontre a été supprimée avec succès!');
        }

        return $this->redirectToRoute('app_rencontre_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/category/{category}', name: 'app_rencontre_by_category', methods: ['GET'])]
    public function byCategory(string $category, RencontreRepository $rencontreRepository): Response
    {
        $rencontres = $rencontreRepository->findBy(['categorie_rencontre' => $category]);
        
        return $this->render('rencontre/index.html.twig', [
            'rencontres' => $rencontres,
            'category' => $category
        ]);
    }
}
