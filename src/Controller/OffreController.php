<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Entity\User;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    #[Route('/autocomplete', name: 'app_offre_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, OffreRepository $offreRepository): JsonResponse
    {
        $term = $request->query->get('term', '');
        if (strlen($term) < 2) {
            return new JsonResponse([]);
        }

        $results = $offreRepository->createQueryBuilder('o')
            ->select('DISTINCT o.titre, o.description, o.organisation')
            ->where('o.titre LIKE :term OR o.description LIKE :term OR o.organisation LIKE :term')
            ->setParameter('term', "%$term%")
            ->getQuery()
            ->getResult();

        $suggestions = [];
        foreach ($results as $result) {
            if ($result['titre'] && !in_array($result['titre'], $suggestions)) {
                $suggestions[] = $result['titre'];
            }
            if ($result['organisation'] && !in_array($result['organisation'], $suggestions)) {
                $suggestions[] = $result['organisation'];
            }
        }

        return new JsonResponse($suggestions);
    }

    #[Route('/', name: 'app_offre_index', methods: ['GET'])]
public function index(Request $request, OffreRepository $offreRepository, PaginatorInterface $paginator): Response
{
    try {
        // Fetch categories
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

        // Fetch regions
        $regionsResult = $offreRepository->createQueryBuilder('o')
            ->select('DISTINCT o.gouvernorat')
            ->where('o.gouvernorat IS NOT NULL')
            ->orderBy('o.gouvernorat', 'ASC')
            ->getQuery()
            ->getResult();

        $regions = array_map(fn($row) => $row['gouvernorat'], $regionsResult);

        // Build query for offers
        $queryBuilder = $offreRepository->createQueryBuilder('o');

        $filters = [
            'search' => $request->query->get('search', ''),
            'category' => $request->query->get('category', ''),
            'region' => $request->query->get('region', ''),
            'salary' => $request->query->getInt('salary', 0),
            'sort' => $request->query->get('sort', 'recent'),
        ];

        if ($filters['search']) {
            $queryBuilder->andWhere('o.titre LIKE :search OR o.description LIKE :search OR o.organisation LIKE :search')
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
                ->andWhere('o.salaire IS NOT NULL')
                ->setParameter('salary', $filters['salary']);
        }

        // Validate sort parameter
        $validSorts = ['recent', 'salary-desc', 'salary-asc'];
        $sort = in_array($filters['sort'], $validSorts) ? $filters['sort'] : 'recent';

        // Debug the sort value
        dump($sort);

        // Reset any existing ORDER BY clauses to avoid conflicts
        $queryBuilder->resetDQLPart('orderBy');

        // Apply sorting
        switch ($sort) {
            case 'salary-desc':
                $queryBuilder->addSelect('CASE WHEN o.salaire IS NULL THEN 1 ELSE 0 END AS HIDDEN is_salaire_null')
                    ->addOrderBy('o.salaire', 'DESC')
                    ->addOrderBy('is_salaire_null', 'ASC'); // Push NULLs to the end
                break;
            case 'salary-asc':
                $queryBuilder->addSelect('CASE WHEN o.salaire IS NULL THEN 1 ELSE 0 END AS HIDDEN is_salaire_null')
                    ->addOrderBy('o.salaire', 'ASC')
                    ->addOrderBy('is_salaire_null', 'DESC'); // Push NULLs to the end
                break;
            case 'recent':
            default:
                $queryBuilder->addOrderBy('o.date_publication', 'DESC');
                break;
        }

        // Debug the SQL query
        $sql = $queryBuilder->getQuery()->getSQL();
        dump($sql);

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            9,
            [
                'defaultSortFieldName' => null,
                'defaultSortDirection' => 'asc',
                'sortFieldParameterName' => null,
            ]
        );

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'offersHtml' => $this->renderView('offre/_offers_grid.html.twig', [
                    'offres' => $pagination,
                ]),
            ]);
        }

        return $this->render('offre/index.html.twig', [
            'offres' => $pagination,
            'categories' => $categories,
            'regions' => $regions,
            'filters' => $filters,
        ]);
    } catch (\Exception $e) {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
        throw $e;
    }
}

#[Route('/{id_offre<\d+>}', name: 'app_offre_show', methods: ['GET'])]
public function show(int $id_offre, OffreRepository $offreRepository, CandidatureRepository $candidatureRepository): Response
{
    $offre = $offreRepository->find($id_offre);
    if (!$offre) {
        throw new NotFoundHttpException('Offre non trouvée.');
    }

    $recommendedOffers = [];
    $maxResults = 8;

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

    // Fetch candidatures and sort by score (DESC), pushing NULL scores to the end
    $candidatures = $candidatureRepository->createQueryBuilder('c')
        ->where('c.offre = :offre')
        ->setParameter('offre', $offre)
        ->addSelect('CASE WHEN c.score IS NULL THEN 1 ELSE 0 END AS HIDDEN is_score_null')
        ->addOrderBy('is_score_null', 'ASC') // Push NULLs to the end
        ->addOrderBy('c.score', 'DESC') // Sort by score in descending order
        ->getQuery()
        ->getResult();

    return $this->render('offre/show.html.twig', [
        'offre' => $offre,
        'candidatures' => $candidatures,
        'recommendedOffers' => $recommendedOffers,
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
        $form = $this->createForm(OffreType::class, $offre, [
            'selected_gouvernorat' => $offre->getGouvernorat(),
        ]);

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

        $regionsAndCities = OffreType::getRegionsAndCities();
        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
            'regionsAndCities' => $regionsAndCities,
            'selectedGouvernorat' => $offre->getGouvernorat(),
            'selectedVille' => $offre->getVille(),
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
