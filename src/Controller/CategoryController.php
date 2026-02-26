<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategoryFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategorieRepository $repo): Response
    {

        $categories = $repo->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/category/create', name: 'app_category_create')]
    public function addCategory(EntityManagerInterface $entityManager, Request $request) : Response
    {

        $category = new Categorie();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie à bien été crée.'); //? créer un tableau temporaire contenant un message d'info transmissible au front
            return $this->redirectToRoute('app_category', []); //? actualisation de page pour mettre en place l'affichage (obligatoire sinon le msg s'affiche pas)
        }

        return $this->render('category/newCategory.html.twig', [
        'form' => $form->createView(),
        
        ]);
    }

    #[Route('/admin/category/update/{id}', name: 'app_category_update')]
    public function updateCategory(Categorie $category, EntityManagerInterface $entityManager, Request $request) : Response
    { //? je mets Categorie en paramètre et pas en variable instanciée comme ça l'appli va prendre en compte que je veux travailler sur un objet qui EXISTE déjà
    //? alors que si j'instancie la classe Categorie dans une variable, l'appli va prendre en compte que je veux créer un objet vide et l'INJECTER dans la bdd (create du crud)

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('info', 'La catégorie à bien été modifiée.'); //? créer un tableau temporaire contenant un message d'info transmissible au front (une fois affichée, le msg est supprimé du tableau)
            return $this->redirectToRoute('app_category'); //? actualisation de page pour mettre en place l'affichage (obligatoire sinon le msg s'affiche pas)
                
            
        }

        return $this->render('category/updateCategory.html.twig', [
        'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/remove/{id}', name: 'app_category_remove')]
    public function deleteCategory(Categorie $category, EntityManagerInterface $entityManager)
    {


    $entityManager->remove($category);
    $entityManager->flush();
    $this->addFlash('danger', 'La catégorie à bien été supprimée');

    return $this->redirectToRoute('app_category');
    }
}
