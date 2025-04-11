<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Entity\User;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    #[Route(name: 'app_offre_index', methods: ['GET'])]
    public function index(OffreRepository $offreRepository): Response
    {
        $categories = [
            'Marketing & Communication' => '0',
            'Sponsoring & Partenariats' => '2',
            'Animation & Production Artistique' => '0',
            'Technique & Audiovisuel' => '14',
            'Logistique & Opérations' => '1',
            'Relationnel & Accueil' => '3',
            'Digital & Innovation' => '22',
        ];

        $regions = ['Tunis', 'Sousse', 'Sfax', 'Gabes'];

        return $this->render('offre/index.html.twig', [
            'offres' => $offreRepository->findAll(),
            'categories' => $categories,
            'regions' => $regions
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new Offre();
        $offre->setUser($entityManager->getReference(User::class, 1));
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                $fileContent = file_get_contents($logoFile->getPathname());
                $offre->setOrganisationLogo($fileContent);
            }
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre, OffreRepository $offreRepository, CandidatureRepository $candidatureRepository): Response
{
    $recommendedOffers = [];
    $maxResults = 4; // Number of recommended offers we want
    
    // 1. First try: Same category
    $recommendedOffers = $offreRepository->createQueryBuilder('o')
        ->where('o.categorie = :categorie')
        ->andWhere('o.id_offre != :currentId')
        ->setParameter('categorie', $offre->getCategorie())
        ->setParameter('currentId', $offre->getIdOffre())
        ->orderBy('o.date_publication', 'DESC')
        ->setMaxResults($maxResults)
        ->getQuery()
        ->getResult();

    // 2. If not enough, add offers from same ville
    if (count($recommendedOffers) < $maxResults) {
        $remaining = $maxResults - count($recommendedOffers);
        $villeOffers = $offreRepository->createQueryBuilder('o')
            ->where('o.ville = :ville')
            ->andWhere('o.id_offre != :currentId')
            ->andWhere('o NOT IN (:existingOffers)')
            ->setParameter('ville', $offre->getVille())
            ->setParameter('currentId', $offre->getIdOffre())
            ->setParameter('existingOffers', $recommendedOffers)
            ->orderBy('o.date_publication', 'DESC')
            ->setMaxResults($remaining)
            ->getQuery()
            ->getResult();

        $recommendedOffers = array_merge($recommendedOffers, $villeOffers);
    }

    // 3. If still not enough, add offers from same gouvernorat
    if (count($recommendedOffers) < $maxResults) {
        $remaining = $maxResults - count($recommendedOffers);
        $gouvernoratOffers = $offreRepository->createQueryBuilder('o')
            ->where('o.gouvernorat = :gouvernorat')
            ->andWhere('o.id_offre != :currentId')
            ->andWhere('o NOT IN (:existingOffers)')
            ->setParameter('gouvernorat', $offre->getGouvernorat())
            ->setParameter('currentId', $offre->getIdOffre())
            ->setParameter('existingOffers', $recommendedOffers)
            ->orderBy('o.date_publication', 'DESC')
            ->setMaxResults($remaining)
            ->getQuery()
            ->getResult();

        $recommendedOffers = array_merge($recommendedOffers, $gouvernoratOffers);
    }

    // 4. If still not enough, add offers with same type contrat
    if (count($recommendedOffers) < $maxResults) {
        $remaining = $maxResults - count($recommendedOffers);
        $typeContratOffers = $offreRepository->createQueryBuilder('o')
            ->where('o.type_contrat = :typeContrat')
            ->andWhere('o.id_offre != :currentId')
            ->andWhere('o NOT IN (:existingOffers)')
            ->setParameter('typeContrat', $offre->getTypeContrat())
            ->setParameter('currentId', $offre->getIdOffre())
            ->setParameter('existingOffers', $recommendedOffers)
            ->orderBy('o.date_publication', 'DESC')
            ->setMaxResults($remaining)
            ->getQuery()
            ->getResult();

        $recommendedOffers = array_merge($recommendedOffers, $typeContratOffers);
    }

    $candidatures = $candidatureRepository->findBy(['offre' => $offre]);

    return $this->render('offre/show.html.twig', [
        'offre' => $offre,
        'candidatures' => $candidatures,
        'recommendedOffers' => $recommendedOffers,
    ]);
}

    #[Route('/{id_offre}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getId_offre(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
}
