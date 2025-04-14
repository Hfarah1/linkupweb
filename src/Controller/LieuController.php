<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'lieu')]
    public function index(LieuRepository $lieuRepository): Response
    {
        $lieux = $lieuRepository->findAll();

        return $this->render('lieu/lieux.html.twig', [
            'Lieux' => $lieux,
        ]);
    }

    #[Route('/lieuxlist', name: 'lieuxlist')]
    public function lieulist(LieuRepository $lieuRepository): Response
    {
        $lieux = $lieuRepository->findAll();

        return $this->render('lieu/lieux.html.twig', [
            'Lieux' => $lieux,
        ]);
    }

    #[Route('/newlieu', name: 'new_lieu')]
    public function newLieu(ManagerRegistry $mr, Request $request): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
            $em->persist($lieu);
            $em->flush();

            return $this->redirectToRoute('lieu');
        }

        return $this->render('lieu/new_lieu.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/newLieuFront', name: 'new_lieu_front')]
    public function newLieuFront(LieuRepository $lieuRepository): Response
    {
        $lieux = $lieuRepository->findAll();

        return $this->render('basefront.html.twig', [
            'Lieux' => $lieux,
        ]);
    }

    #[Route('/lieu/edit/{id}', name: 'lieu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('lieu');
        }

        return $this->render('lieu/edit.html.twig', [
            'form' => $form->createView(),
            'lieu' => $lieu,
        ]);
    }

    #[Route('/lieu/delete/{id}', name: 'lieu_delete', methods: ['GET', 'POST'])]
    public function deleteLieu($id, ManagerRegistry $managerRegistry, LieuRepository $lieuRepository): Response
    {
        $entityManager = $managerRegistry->getManager();
        $lieu = $lieuRepository->find($id);

        if ($lieu) {
            $entityManager->remove($lieu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lieu', [], Response::HTTP_SEE_OTHER);
    }
}
