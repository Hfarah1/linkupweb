<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Service\UploadService;
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

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'product')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        return $this->render('product/products.html.twig', [
            'produits' => $produits,
        ]);
    }
    
    #[Route('/productlist', name: 'productlist')]
    public function productlist(ProduitRepository $produitRepository): Response
    {
        $products = $produitRepository->findAll();

        return $this->render('product/productsback.html.twig', [
            'products' => $products,
        ]);
    }
    

/*
    #[Route('/form', name: 'form')]
    public function form(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('price', MoneyType::class, [
                'currency' => 'TND',
                'divisor' => 1,
            ])
            ->add('description', TextareaType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Physical' => 'physique',
                    'Service' => 'service',
                ],
                'placeholder' => '--Please choose an option--',
            ])
            ->add('value', TextType::class) // Quantity or Duration
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            dd($data); // replace with saving logic later
        }
    
        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   */


/*
    #[Route('/newproduct', name: 'newproduct')]
    public function newproduct(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => ['placeholder' => 'Enter product name']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Enter product description']
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'attr' => ['placeholder' => 'Enter product price']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Physique' => 'physique',
                    'Service' => 'service',
                ],
                'placeholder' => 'Select type'
            ])
            ->add('value', TextType::class, [
                'label' => 'Value',
                'attr' => ['placeholder' => 'Enter value']
            ])
            ->add('image', FileType::class, [
                'label' => 'Upload Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'OK'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // Juste pour test :
            $this->addFlash('success', 'Produit reçu : ' . $data['name']);
            return $this->redirectToRoute('newproduct');
        }

        return $this->render('product/newproduct.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/
  
    #[Route('/newProduit', name: 'newProduit')]
    public function newProduit(ManagerRegistry $mr, Request $req, UploadService $uploadService): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
    
            // Récupérer l'image du produit
            $fileImg = $form->get('image')->getData();
            if ($fileImg) {
                $fileNameImg = $uploadService->uploadFile($fileImg);  // Utiliser le service pour l'upload
                $produit->setImage($fileNameImg);
            }
    
            $produit->setCreatedAt(new \DateTimeImmutable());
            $produit->setUpdatedAt(new \DateTimeImmutable());
    
            // Enregistrement en base
            $em->persist($produit);
            $em->flush();
    
            return $this->redirectToRoute('productlist');
        }
    
        return $this->render('product/newproduct.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/newProduitFront', name: 'newProduitFront')]
    public function newProduitFront(ManagerRegistry $mr, Request $req, UploadService $uploadService): Response
    {
        // Création d'un nouvel objet produit
        $produit = new Produit();
        
        // Création du formulaire
        $form = $this->createForm(ProduitType::class, $produit);
        
        // Traitement de la soumission du formulaire
        $form->handleRequest($req);
    
        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getErrors(true, true));
            $em = $mr->getManager();
            
            // Gestion de l'image du produit
            $fileImg = $form->get('image')->getData();
            if ($fileImg) {
                // Utilisation du service pour uploader l'image
                $fileNameImg = $uploadService->uploadFile($fileImg);
                $produit->setImage($fileNameImg);  // Enregistrement du nom du fichier dans l'entité
            }
    
            // Ajout des dates de création et de mise à jour
            $produit->setCreatedAt(new \DateTimeImmutable());
            $produit->setUpdatedAt(new \DateTimeImmutable());
    
            // Enregistrement du produit en base de données
            $em->persist($produit);
            $em->flush();
    
            // Redirection après l'ajout du produit
            return $this->redirectToRoute('product');
        }
    
        // Affichage du formulaire
        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/produit/edit/{id}', name: 'produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $product, EntityManagerInterface $entityManager, UploadService $uploadService): Response
    {
        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'image envoyée par le formulaire
            $file = $form->get('image')->getData();

            // Vérifier si un fichier a été téléchargé
            if ($file) {
                // Supprimer l'ancienne image si elle existe
                if ($product->getImage()) {
                    $oldImagePath = $this->getParameter('upload_directory') . '/' . $product->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Uploader la nouvelle image
                $newImageName = $uploadService->uploadFile($file);
                $product->setImage($newImageName);
            }

            // Mettre à jour les informations du produit
            $product->setUpdatedAt(new \DateTime());

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Rediriger vers la page de détail du produit ou vers la liste des produits
            return $this->redirectToRoute('productlist');
        }

        return $this->render('product/editback.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/produit/delete/{id}', name: 'produit_delete', methods: ['GET', 'POST'])]
public function deleteProduit($id, ManagerRegistry $managerRegistry, ProduitRepository $produitRepository): Response
{
    $entityManager = $managerRegistry->getManager();
    
    // Find the produit by ID
    $produit = $produitRepository->find($id);
    
    // Check if the produit exists
    if ($produit) {
        // Remove the produit from the database
        $entityManager->remove($produit);
        $entityManager->flush();
    }
    
    // Redirect to the list page of produits after deletion
    return $this->redirectToRoute('productlist', [], Response::HTTP_SEE_OTHER);
}



}