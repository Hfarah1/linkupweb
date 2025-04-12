<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\User;
use App\Entity\Offre;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre/{id_offre}/candidature')]
final class CandidatureController extends AbstractController
{
    #[Route(name: 'app_candidature_index', methods: ['GET'])]
    public function index(CandidatureRepository $candidatureRepository): Response
    {
        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_candidature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Offre $offre, CandidatureRepository $candidatureRepository): Response
    {
        $candidature = new Candidature();
        $candidature->setUser($entityManager->getReference(User::class, 1));
        $candidature->setOffre($offre);
        $candidature->setStatut('En attente');
        $candidature->setDateCandidature(new \DateTime());
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cvFile = $form->get('cv_upload')->getData();

            if ($cvFile) {
                // Store the file content in the database as BLOB
                $candidature->setCvFile(file_get_contents($cvFile->getPathname()));
            }

            $entityManager->persist($candidature);
            $entityManager->flush();
            $this->addFlash('success', 'Votre candidature a été soumise avec succès');
            return $this->redirectToRoute('app_offre_show', [
                'id_offre' => $offre->getIdOffre()
            ]);
        }

        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id_candidature}', name: 'app_candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature, Offre $offre): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
            'offre' => $offre,
        ]);
    }

    #[Route('/{id_candidature}/edit', name: 'app_candidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id_candidature}', name: 'app_candidature_delete', methods: ['POST'])]
    public function delete(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidature->getId_candidature(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id_candidature}/view-cv', name: 'app_candidature_view_cv')]
    public function viewCv(Candidature $candidature): Response
    {
        $cvContent = $candidature->getCvFile();

        if (is_resource($cvContent)) {
            $cvContent = stream_get_contents($cvContent);
        }

        return new Response($cvContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="cv_' . $candidature->getId_candidature() . '.pdf"'
        ]);
    }

    #[Route('/{id_candidature}/download-cv', name: 'app_candidature_download_cv')]
    public function downloadCv(Candidature $candidature): Response
    {
        $cvContent = $candidature->getCvFile();

        if (is_resource($cvContent)) {
            $cvContent = stream_get_contents($cvContent);
        }

        return new Response($cvContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="cv_' . $candidature->getIdCandidature() . '.pdf"'
        ]);
    }
}
