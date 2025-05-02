<?php
namespace App\Controller;

use App\Entity\Materiel;
use App\Form\MaterielType;
use App\Service\DeepAiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaterielController extends AbstractController
{
    #[Route('/materiel/new', name: 'materiel_new')]
    public function new(Request $request, EntityManagerInterface $em, DeepAiService $deepAiService): Response
    {
        $materiel = new Materiel();
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);

        // Étape 1 : Générer les images
        if ($form->isSubmitted() && $form->isValid() && !$request->request->get('selected_image')) {
            $images = [];
            for ($i = 0; $i < 3; $i++) {
                $img = $deepAiService->generateImage($materiel->getDescriptionMat());
                $images[] = $img;
            }
            dump($images); // Debug : voir ce que retourne l'API
            return $this->render('materiel/choose_image.html.twig', [
                'form' => $form->createView(),
                'images' => $images,
                'materiel' => $materiel,
            ]);
        }

        // Étape 2 : Sauvegarder le matériel avec l'image choisie
        if ($request->isMethod('POST') && $request->request->get('selected_image')) {
            $materiel->setImagePath($request->request->get('selected_image'));
            $em->persist($materiel);
            $em->flush();
            $this->addFlash('success', 'Matériel créé avec l\'image choisie !');
            return $this->redirectToRoute('materiel_list');
        }

        return $this->render('materiel/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 