<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Entity\User;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    #[Route('/{id_offre<\d+>}', name: 'app_offre_show', methods: ['GET'])]
    public function show(int $id_offre, OffreRepository $offreRepository, CandidatureRepository $candidatureRepository): Response
    {
        $offre = $offreRepository->find($id_offre);
        if (!$offre) {
            throw new NotFoundHttpException('Offre non trouvée.');
        }

        $recommendedOffers = [];
        $maxResults = 4;

        // Same category
        $recommendedOffers = $offreRepository->createQueryBuilder('o')
            ->where('o.categorie = :categorie')
            ->andWhere('o.id_offre != :currentId')
            ->setParameter('categorie', $offre->getCategorie())
            ->setParameter('currentId', $offre->getIdOffre())
            ->orderBy('o.date_publication', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();

        // Same ville
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

        // Same gouvernorat
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

        // Same type contrat
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

    #[Route('/{page<\d+>}', name: 'app_offre_index', methods: ['GET'], defaults: ['page' => 1])]
    public function index(Request $request, OffreRepository $offreRepository, int $page): Response
    {
        // Fetch distinct categories and their counts
        $categoriesResult = $offreRepository->createQueryBuilder('o')
            ->select('o.categorie, COUNT(o.id_offre) as offer_count')
            ->groupBy('o.categorie')
            ->getQuery()
            ->getResult();

        $categories = [];
        foreach ($categoriesResult as $result) {
            if ($result['categorie']) {
                $categories[$result['categorie']] = (int) $result['offer_count'];
            }
        }

        // Fetch distinct regions (gouvernorat)
        $regionsResult = $offreRepository->createQueryBuilder('o')
            ->select('DISTINCT o.gouvernorat')
            ->where('o.gouvernorat IS NOT NULL')
            ->orderBy('o.gouvernorat', 'ASC')
            ->getQuery()
            ->getResult();

        $regions = array_map(fn($row) => $row['gouvernorat'], $regionsResult);

        // Pagination parameters
        $itemsPerPage = 9;
        $queryBuilder = $offreRepository->createQueryBuilder('o');

        // Apply filters
        $filters = [
            'search' => $request->query->get('search', ''),
            'category' => $request->query->get('category', ''),
            'region' => $request->query->get('region', ''),
            'salary' => $request->query->getInt('salary', 0),
            'sort' => $request->query->get('sort', 'recent'),
        ];

        if ($filters['search']) {
            $queryBuilder->andWhere('o.titre LIKE :search OR o.description LIKE :search')
                ->setParameter('search', "%{$filters['search']}%");
        }
        if ($filters['category']) {
            $queryBuilder->andWhere('o.categorie = :category')
                ->setParameter('category', $filters['category']);
        }
        if ($filters['region']) {
            $queryBuilder->andWhere('o.gouvernorat = :region')
                ->setParameter('region', $filters['region']);
        }
        if ($filters['salary'] > 0) {
            $queryBuilder->andWhere('o.salaire >= :salary')
                ->setParameter('salary', $filters['salary']);
        }

        // Apply sorting
        switch ($filters['sort']) {
            case 'salary-desc':
                $queryBuilder->orderBy('o.salaire', 'DESC');
                break;
            case 'salary-asc':
                $queryBuilder->orderBy('o.salaire', 'ASC');
                break;
            case 'recent':
            default:
                $queryBuilder->orderBy('o.date_publication', 'DESC');
                break;
        }

        // Paginate
        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        $paginator = new Paginator($query, true);
        $totalItems = count($paginator);
        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        return $this->render('offre/index.html.twig', [
            'offres' => $paginator,
            'categories' => $categories,
            'regions' => $regions,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters,
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $offre = new Offre();
    $offre->setUser($entityManager->getReference(User::class, 1));
    $form = $this->createForm(OffreType::class, $offre);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                $fileContent = file_get_contents($logoFile->getPathname());
                $offre->setOrganisationLogo($fileContent);
            }
            $entityManager->persist($offre);
            $entityManager->flush();
            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    $regionsAndCities = OffreType::getRegionsAndCities();

    return $this->render('offre/new.html.twig', [
        'offre' => $offre,
        'form' => $form,
        'regionsAndCities' => $regionsAndCities,
    ]);
}

    #[Route('/{id_offre}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logoFile')->getData();
            if ($logoFile) {
                $fileContent = file_get_contents($logoFile->getPathname());
                $offre->setOrganisationLogo($fileContent);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($offre);
        $entityManager->flush();

        $this->addFlash('success', 'Offre supprimée avec succès');
        return $this->redirectToRoute('app_offre_index');
    }
}