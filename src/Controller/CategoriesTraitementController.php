<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Car;
use App\Entity\Category;
use app\Entity\Rental;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\CategoryFormType;
use Symfony\Component\HttpFoundation\Request;



class CategoriesTraitementController extends AbstractController
{
    #[Route('/categories/traitement', name: 'app_categories_traitement')]
    public function index(): Response
    {
        return $this->render('categories_traitement/index.html.twig', [
            'controller_name' => 'CategoriesTraitementController',
        ]);
    }
  
    #[Route('/categories', name: 'app_categories')]
    public function listcategory(CategoryRepository $category): Response
    {
        // Récupérer tous les clients
        $categories = $category->findAll();

        return $this->render('categorieslist.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('/categories/{id}/delete', name: 'app_category_delete')]
    public function deletecategory(Category $category, EntityManagerInterface $entityManager): Response
    {
      
          $cars = $entityManager->getRepository(Car::class)->findBy(['category' => $category]);
         

          
        
        // Supprimer les locations associées
        foreach ($cars as $car) {
            $rentals = $entityManager->getRepository(Rental::class)->findBy(['car' => $car]);

            // Supprimer les locations associées
            foreach ($rentals as $rental) {
                $entityManager->remove($rental);  // Supprimer chaque location associée
            }
            $entityManager->remove($car);  // Supprimer chaque location associée
        }

        $entityManager->remove($category);
        $entityManager->flush();

        // Rediriger vers la liste des clients
        return $this->redirectToRoute('app_categories');
    }
    #[Route('/category/add', name: 'app_category_add', methods: ['GET'])]
    public function addCategory(): Response
    {
        return $this->render('categoryadd.html.twig'); // Affiche le fichier Twig correspondant
    }
    
    #[Route("/category/create", name:"app_category_create", methods:["POST"])]
    public function createCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');
    
        if (!$name || !$description) {
            $this->addFlash('error', 'All fields are required!');
            return $this->redirectToRoute('app_category_create_form'); // Redirection vers le formulaire
        }
    
        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
    
        $entityManager->persist($category);
        $entityManager->flush();
    
        $this->addFlash('success', 'Category successfully created!');
        return $this->redirectToRoute('app_categories'); // Redirection vers la liste des catégories
    }
    

    #[Route('/category/edit/{id}', name: 'app_category_edit')]
public function editCar(int $id, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
{
    $category = $categoryRepository->find($id);

    if (!$category) {
        throw $this->createNotFoundException('category not found');
    }

    if ($request->isMethod('POST')) {
        $category->setName($request->request->get('name'));
        $category->setDescription($request->request->get('description'));
     

        

        $entityManager->flush();

        $this->addFlash('success', 'Category updated successfully!');
        return $this->redirectToRoute('app_categories'); // Retourner à la liste des clients
    }

   

    return $this->render('categoryedit.html.twig', [
        'category' => $category,
    ]);
}
    #[Route('/category/new_form', name: 'app_category_new_form')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de l'entité Category
        $category = new Category();

        // Créer le formulaire pour l'entité Category
        $form = $this->createForm(CategoryFormType::class, $category);

        // Traiter la requête (si le formulaire a été soumis)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'entité Category dans la base de données
            $entityManager->persist($category);
            $entityManager->flush();

            // Ajouter un message flash pour notifier que l'entité a été enregistrée
            $this->addFlash('success', 'Category created successfully!');

            // Rediriger vers la liste des catégories (ou une autre page)
            return $this->redirectToRoute('app_categories');
        }

        // Rendre la vue avec le formulaire
        return $this->render('new_form_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}


