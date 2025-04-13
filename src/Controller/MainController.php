<?php

namespace App\Controller;

use App\Form\UtilisateurType;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $users=$this->em->getRepository(Utilisateur::class)->findAll();  
        return $this->render('main/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create-user', name: 'create-user')]
    public function createUser(Request $request){
     
        $user=new Utilisateur();
        $form=$this->createForm(UtilisateurType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid() ){
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('message','Inserted sucessfully.');
            return $this->redirectToRoute('app_main');
        } 

        return $this->render('main/user.html.twig',['form' => $form->createView()]);

    }

    #[Route('edit-user/{id}', name: 'edit-user')]
    public function editUser(Request $request,$id){
        $user= $this->em->getRepository(Utilisateur::class)->find($id);

        $form = $this->createForm(UtilisateurType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('message','Updated sucessfully.');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('main/user.html.twig',[
            'form'=>$form->createView() 
        ]);

    }
    #[Route('delete-user/{id}', name: 'delete-user')]
    public function deletePost($id){

        $user=$this->em->getRepository(Utilisateur::class)->find($id);
        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('message','Deleted sucessfully.');
        return $this->redirectToRoute('app_main');


    }


}
