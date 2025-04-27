<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Repository\FeedbackRepository;
use App\Repository\RencontreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/feedback')]
final class FeedbackController extends AbstractController
{
    #[Route('/new/{id_rencontre}', name: 'app_feedback_new', methods: ['POST'])]
    public function new(
        Request $request, 
        int $id_rencontre, 
        EntityManagerInterface $entityManager,
        RencontreRepository $rencontreRepository
    ): Response {
        // Check CSRF token
        if (!$this->isCsrfTokenValid('feedback'.$id_rencontre, $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_rencontre_show', ['id_rencontre' => $id_rencontre]);
        }
        
        // Find the rencontre
        $rencontre = $rencontreRepository->find($id_rencontre);
        if (!$rencontre) {
            throw $this->createNotFoundException('Rencontre non trouvée');
        }
        
        // Create new feedback
        $feedback = new Feedback();
        $feedback->setIdRencontre($id_rencontre);
        $feedback->setIdUtilisateur($request->request->get('id_utilisateur'));
        $feedback->setCommentaire($request->request->get('contenu'));
        $feedback->setDateCommentaire(new \DateTime());
        
        // Add score if available
        if ($request->request->has('score')) {
            $feedback->setNote($request->request->getInt('score'));
        }
        
        $entityManager->persist($feedback);
        $entityManager->flush();
        
        $this->addFlash('success', 'Votre commentaire a été ajouté avec succès!');
        return $this->redirectToRoute('app_rencontre_show', ['id_rencontre' => $id_rencontre]);
    }
    
    #[Route('/reply/{id_parent}/{id_rencontre}', name: 'app_feedback_reply', methods: ['POST'])]
    public function reply(
        Request $request, 
        int $id_parent, 
        int $id_rencontre,
        EntityManagerInterface $entityManager,
        FeedbackRepository $feedbackRepository
    ): Response {
        // Check CSRF token
        if (!$this->isCsrfTokenValid('reply_feedback'.$id_parent, $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_rencontre_show', ['id_rencontre' => $id_rencontre]);
        }
        
        // Find parent feedback
        $parentFeedback = $feedbackRepository->find($id_parent);
        if (!$parentFeedback) {
            throw $this->createNotFoundException('Commentaire parent non trouvé');
        }
        
        // Create reply
        $reply = new Feedback();
        $reply->setIdRencontre($id_rencontre);
        $reply->setIdUtilisateur($request->request->get('id_utilisateur'));
        $reply->setCommentaire($request->request->get('contenu'));
        $reply->setDateCommentaire(new \DateTime());
        $reply->setReplyTo($id_parent);
        
        $entityManager->persist($reply);
        $entityManager->flush();
        
        $this->addFlash('success', 'Votre réponse a été ajoutée avec succès!');
        return $this->redirectToRoute('app_rencontre_show', ['id_rencontre' => $id_rencontre]);
    }
    
    #[Route('/delete/{id}', name: 'app_feedback_delete', methods: ['GET'])]
    public function delete(
        int $id, 
        FeedbackRepository $feedbackRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $feedback = $feedbackRepository->find($id);
        
        if (!$feedback) {
            throw $this->createNotFoundException('Commentaire non trouvé');
        }
        
        // Get rencontre ID for redirect
        $rencontreId = $feedback->getIdRencontre();
        
        // Delete any replies first
        $replies = $feedbackRepository->findReplies($id);
        foreach ($replies as $reply) {
            $entityManager->remove($reply);
        }
        
        // Delete the feedback
        $entityManager->remove($feedback);
        $entityManager->flush();
        
        $this->addFlash('success', 'Le commentaire a été supprimé avec succès!');
        return $this->redirectToRoute('app_rencontre_show', ['id_rencontre' => $rencontreId]);
    }
} 