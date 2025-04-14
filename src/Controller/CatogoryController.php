<?php

namespace App\Controller;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Service\UploadService;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;




final class CatogoryController extends AbstractController
{
    #[Route('/catogory', name: 'category')]
    public function index(CategorieRepository $CatgeorieRepository): Response
    {
        $categories = $CategorieRepository->findAll();
        return $this->render('catogory/categories.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('/categorietlist', name: 'categorietlist')]
    public function categorielist(CategorieRepository $CategorieRepository): Response
    {
        $categories = $CategorieRepository->findAll();
        return $this->render('catogory/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/newcategory', name: 'new')]
    public function newcategory(ManagerRegistry $mr, Request $req , SluggerInterface $slugger): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
    
            
            $fileImg = $form->get('description')->getData();
            if ($fileImg) {
                $originalFilename = pathinfo($fileImg->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fileImg->guessExtension();
            
                
                try {
                    $fileImg->move(
                        $this->getParameter('upload_directory'), 
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur
                }
            
                // Enregistrer juste le nom du fichier ou son chemin
                $categorie->setDescription($newFilename);
            }
            // Enregistrement en base
            $em->persist($categorie);
            $em->flush();
    
            return $this->redirectToRoute('categorietlist');
        }
    
        return $this->render('catogory/newcategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }

  
    #[Route('/newCategoryFront', name: 'newCategoryFront')]
    public function newCategoryFront(CategorieRepository $CategorieRepository): Response
    {
        $categories = $CategorieRepository->findAll();
        return $this->render('basefront.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/back', name: 'newEventCat')]
    public function newEventCat(CategorieRepository $CategorieRepository): Response
    {
        $categories = $CategorieRepository->findAll();
        return $this->render('/catogory/categoriesback.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('/category/edit/{id}', name: 'category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $c, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategorieType::class, $c);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $fileImg = $form->get('description')->getData();

            
            if ($fileImg) {
                $originalFilename = pathinfo($fileImg->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fileImg->guessExtension();
            
                
                try {
                    $fileImg->move(
                        $this->getParameter('upload_directory'), 
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }
            
                
                $c->setDescription($newFilename);
            }

            

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Rediriger vers la page de détail du produit ou vers la liste des produits
            return $this->redirectToRoute('categorietlist');
        }

        return $this->renderForm('catogory/newcategory.html.twig', [
            'form' => $form->createView(),
            'categorie' => $c,
        ]);
    }


    #[Route('/category/delete/{id}', name: 'category_delete', methods: ['GET', 'POST'])]
    public function deleteCategory($id, ManagerRegistry $managerRegistry, CategorieRepository $CategorieRepository): Response
    {
        $entityManager = $managerRegistry->getManager();
        
        // Find the category by ID
        $cat = $CategorieRepository->find($id);
        
        // Check if the category exists
        if ($cat) {
            // Remove the produit from the database
            $entityManager->remove($cat);
            $entityManager->flush();
        }
        
        // Redirect to the list page of produits after deletion
        return $this->redirectToRoute('categorietlist', [], Response::HTTP_SEE_OTHER);
    }
    









}
