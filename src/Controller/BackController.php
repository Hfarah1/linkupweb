<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackController extends AbstractController
{
    #[Route('/back', name: 'back')]
    public function index(): Response
    {
        // Static user data for the template
        $user = [
            'name' => 'Ahmed Yassine Benothmen',
            'email' => 'AhmedYassine.Benothmen@Esprit.tn',
        ];

        return $this->render('baseback.html.twig', [
            'controller_name' => 'BackController',
            'user' => $user, // Pass the user data to the template
        ]);
    }

    #[Route('/back/charts', name: 'app_chartjs')]
    public function charts(): Response
    {
        // Same static user data
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