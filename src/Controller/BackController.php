<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Entity\Candidature;
use App\Entity\User;
use App\Form\OffreType;
use App\Form\CandidatureType;
use App\Repository\OffreRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackController extends AbstractController
{
    #[Route('/back', name: 'back', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        OffreRepository $offreRepository,
        CandidatureRepository $candidatureRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Static user data
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        // Filters from query parameters
        $filters = [
            'offer_search' => $request->query->get('offer_search', ''),
            'offer_category' => $request->query->get('offer_category', ''),
            'offer_region' => $request->query->get('offer_region', ''),
            'offer_salary' => $request->query->getInt('offer_salary', 0),
            'offer_sort' => $request->query->get('offer_sort', 'recent'),
            'application_search' => $request->query->get('application_search', ''),
            'application_status' => $request->query->get('application_status', ''),
        ];

        // Pagination parameters
        $itemsPerPage = 9; // Match OffreController
        $offerPage = $request->query->getInt('offer_page', 1);
        $applicationPage = $request->query->getInt('application_page', 1);

        // Statistics
        $stats = [
            'total_offers' => $offreRepository->count([]),
            'total_applications' => $candidatureRepository->count([]),
            'active_offers' => $offreRepository->count(['statut' => 'Active']),
            'pending_applications' => $candidatureRepository->count(['statut' => 'En attente']),
            'accepted_applications' => $candidatureRepository->count(['statut' => 'Acceptée']),
            'rejected_applications' => $candidatureRepository->count(['statut' => 'Refusée']),
            'offers_change' => '+0', // TODO: Calculate based on historical data
            'applications_change' => '+0', // TODO: Calculate based on historical data
            'active_offers_change' => '+0', // TODO: Calculate based on historical data
            'pending_change' => '+0', // TODO: Calculate based on historical data
        ];

        // Categories for dropdown and chart
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

        // Regions (gouvernorat)
        $regionsResult = $offreRepository->createQueryBuilder('o')
            ->select('DISTINCT o.gouvernorat')
            ->where('o.gouvernorat IS NOT NULL')
            ->orderBy('o.gouvernorat', 'ASC')
            ->getQuery()
            ->getResult();
        $regions = array_map(fn($row) => $row['gouvernorat'], $regionsResult);

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

        // Recent Applications
        $recentApplications = $candidatureRepository->findBy([], ['date_candidature' => 'DESC'], 5);

        // Recent Offers
        $recentOffers = $offreRepository->findBy([], ['date_publication' => 'DESC'], 5);

        // Offers with Filters and Pagination
        $offerQuery = $offreRepository->createQueryBuilder('o');
        if ($filters['offer_search']) {
            $offerQuery->andWhere('o.titre LIKE :search OR o.description LIKE :search')
                ->setParameter('search', '%' . $filters['offer_search'] . '%');
        }
        if ($filters['offer_category']) {
            $offerQuery->andWhere('o.categorie = :category')
                ->setParameter('category', $filters['offer_category']);
        }
        if ($filters['offer_region']) {
            $offerQuery->andWhere('o.gouvernorat = :region')
                ->setParameter('region', $filters['offer_region']);
        }
        if ($filters['offer_salary'] > 0) {
            $offerQuery->andWhere('o.salaire >= :salary')
                ->setParameter('salary', $filters['offer_salary']);
        }

        // Apply sorting
        switch ($filters['offer_sort']) {
            case 'salary-desc':
                $offerQuery->orderBy('o.salaire', 'DESC');
                break;
            case 'salary-asc':
                $offerQuery->orderBy('o.salaire', 'ASC');
                break;
            case 'recent':
            default:
                $offerQuery->orderBy('o.date_publication', 'DESC');
                break;
        }

        $totalOffers = (clone $offerQuery)->select('COUNT(o.id_offre)')->getQuery()->getSingleScalarResult();
        $offerTotalPages = max(1, ceil($totalOffers / $itemsPerPage));
        $offerPage = max(1, min($offerPage, $offerTotalPages));

        $allOffers = $offerQuery
            ->setFirstResult(($offerPage - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();

        $offerPagination = [
            'current_page' => $offerPage,
            'total_pages' => $offerTotalPages,
            'query_params' => $filters + ['application_page' => $applicationPage],
        ];

        // Applications with Filters and Pagination
        $applicationQuery = $candidatureRepository->createQueryBuilder('c')
            ->join('c.offre', 'o')
            ->join('c.user', 'u');
        if ($filters['application_search']) {
            $applicationQuery->andWhere('o.titre LIKE :search OR u.prenom LIKE :search OR u.nom LIKE :search')
                ->setParameter('search', '%' . $filters['application_search'] . '%');
        }
        if ($filters['application_status']) {
            $applicationQuery->andWhere('c.statut = :status')
                ->setParameter('status', $filters['application_status']);
        }

        $totalApplications = (clone $applicationQuery)->select('COUNT(c.id_candidature)')->getQuery()->getSingleScalarResult();
        $applicationTotalPages = max(1, ceil($totalApplications / $itemsPerPage));
        $applicationPage = max(1, min($applicationPage, $applicationTotalPages));

        $allApplications = $applicationQuery
            ->setFirstResult(($applicationPage - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();

        $applicationPagination = [
            'current_page' => $applicationPage,
            'total_pages' => $applicationTotalPages,
            'query_params' => $filters + ['offer_page' => $offerPage],
        ];

        // Handle Offer Creation
        $newOffre = new Offre();
        $offerForm = $this->createForm(OffreType::class, $newOffre);
        $offerForm->handleRequest($request);

        if ($offerForm->isSubmitted() && $offerForm->isValid()) {
            $newOffre->setUser($entityManager->getReference(User::class, 1)); // Adjust user ID
            $newOffre->setDatePublication(new \DateTime());
            $newOffre->setStatut('Active');
            $logoFile = $offerForm->get('logoFile')->getData();
            if ($logoFile) {
                $fileContent = file_get_contents($logoFile->getPathname());
                $newOffre->setOrganisationLogo($fileContent);
            }
            $entityManager->persist($newOffre);
            $entityManager->flush();
            $this->addFlash('success', 'Offer created successfully');
            return $this->redirectToRoute('back');
        }

        // Handle Offer Edit
        $editOffre = null;
        if ($request->query->has('edit_offre')) {
            $editOffre = $offreRepository->find($request->query->getInt('edit_offre'));
            if ($editOffre) {
                $editForm = $this->createForm(OffreType::class, $editOffre);
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $logoFile = $editForm->get('logoFile')->getData();
                    if ($logoFile) {
                        $fileContent = file_get_contents($logoFile->getPathname());
                        $editOffre->setOrganisationLogo($fileContent);
                    }
                    $entityManager->flush();
                    $this->addFlash('success', 'Offer updated successfully');
                    return $this->redirectToRoute('back');
                }
            }
        }

        // Handle Candidature Creation
        $newCandidature = new Candidature();
        $candidatureForm = $this->createForm(CandidatureType::class, $newCandidature);
        $candidatureForm->handleRequest($request);

        if ($candidatureForm->isSubmitted() && $candidatureForm->isValid()) {
            $newCandidature->setUser($entityManager->getReference(User::class, 1)); // Adjust user ID
            $newCandidature->setDateCandidature(new \DateTime());
            $newCandidature->setStatut('En attente');
            $cvFile = $candidatureForm->get('cv_upload')->getData();
            if ($cvFile) {
                $newCandidature->setCvFile(file_get_contents($cvFile->getPathname()));
            }
            $entityManager->persist($newCandidature);
            $entityManager->flush();
            $this->addFlash('success', 'Candidature created successfully');
            return $this->redirectToRoute('back');
        }

        // Handle CV Download
        if ($request->query->has('download_cv')) {
            $candidature = $candidatureRepository->find($request->query->getInt('download_cv'));
            if ($candidature && $candidature->getCvFile()) {
                $cvContent = $candidature->getCvFile();
                if (is_resource($cvContent)) {
                    $cvContent = stream_get_contents($cvContent);
                }
                return new Response($cvContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="cv_' . $candidature->getIdCandidature() . '.pdf"',
                ]);
            }
            $this->addFlash('error', 'CV not found');
            return $this->redirectToRoute('back');
        }

        // Handle Edit and Delete (via POST requests)
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $id = $request->request->get('id');

            if ($action === 'delete_offer' && $id) {
                $offre = $offreRepository->find($id);
                if ($offre) {
                    $entityManager->remove($offre);
                    $entityManager->flush();
                    $this->addFlash('success', 'Offer deleted successfully');
                }
            } elseif ($action === 'delete_candidature' && $id) {
                $candidature = $candidatureRepository->find($id);
                if ($candidature) {
                    $entityManager->remove($candidature);
                    $entityManager->flush();
                    $this->addFlash('success', 'Candidature deleted successfully');
                }
            } elseif ($action === 'edit_candidature' && $id) {
                $candidature = $candidatureRepository->find($id);
                if ($candidature) {
                    $status = $request->request->get('status');
                    if (in_array($status, ['En attente', 'Acceptée', 'Refusée'])) {
                        $candidature->setStatut($status);
                        $entityManager->flush();
                        $this->addFlash('success', 'Candidature updated successfully');
                    }
                }
            }

            return $this->redirectToRoute('back');
        }

        // Recommended Offers for View
        $recommendedOffers = [];
        if ($request->query->has('view_offre')) {
            $offre = $offreRepository->find($request->query->getInt('view_offre'));
            if ($offre) {
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
            }
        }
        // Retrieve active tab parameter
        $activeTab = $request->query->get('active_tab', 'overview');
        
        return $this->render('back/backAhmed.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
            'categories' => $categories,
            'stats' => $stats,
            'recent_applications' => $recentApplications,
            'recent_offers' => $recentOffers,
            'all_offers' => $allOffers,
            'all_applications' => $allApplications,
            'categories' => $categories,
            'regions' => $regions,
            'filters' => $filters,
            'offer_pagination' => $offerPagination,
            'application_pagination' => $applicationPagination,
            'offer_form' => $offerForm->createView(),
            'edit_offer_form' => isset($editForm) ? $editForm->createView() : null,
            'candidature_form' => $candidatureForm->createView(),
            'view_offre' => $request->query->getInt('view_offre', 0),
            'view_candidature' => $request->query->getInt('view_candidature', 0),
            'edit_offre' => $request->query->getInt('edit_offre', 0),
            'recommended_offers' => $recommendedOffers,
            'active_tab' => $activeTab,
        ]);
    }

    #[Route('/back/charts', name: 'app_chartjs')]
    public function charts(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/charts/chartjs.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/ui/buttons', name: 'app_buttons')]
    public function buttons(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/ui-features/buttons.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/ui/dropdowns', name: 'app_dropdowns')]
    public function dropdowns(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/ui-features/dropdowns.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/ui/typography', name: 'app_typography')]
    public function typography(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/ui-features/typography.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/forms/basic-elements', name: 'app_basic_elements')]
    public function basicElements(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/forms/basic_elements.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/tables/basic-table', name: 'app_basic_table')]
    public function basicTable(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/tables/basic-table.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/icons/font-awesome', name: 'app_font_awesome')]
    public function fontAwesome(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/icons/font-awesome.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/samples/blank-page', name: 'app_blank_page')]
    public function blankPage(): Response
    {
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('back/pages/samples/blank-page.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user,
        ]);
    }

    #[Route('/back/samples/error-404', name: 'app_error_404')]
    public function error404(): Response
    {
        return $this->render('back/pages/samples/error-404.html.twig');
    }

    #[Route('/back/samples/error-500', name: 'app_error_500')]
    public function error500(): Response
    {
        return $this->render('back/pages/samples/error-500.html.twig');
    }

    #[Route('/back/samples/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('back/pages/samples/login.html.twig');
    }

    #[Route('/back/samples/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('back/pages/samples/register.html.twig');
    }
}
