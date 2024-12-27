<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Car;
use App\Entity\Category;
use App\Repository\CarRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Rental;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CarFormType;


class CarsTraitementController extends AbstractController
{   

    #[Route('/cars/traitement', name: 'app_cars_traitement')]
    public function index(): Response
    {
        return $this->render('cars_traitement/index.html.twig', [
            'controller_name' => 'CarsTraitementController',
        ]);
    }
    
    #[Route('/cars', name: 'app_cars')]
    public function listcars(CarRepository $car): Response
    {
         // Récupérer tous les clients
         $cars = $car->findAll();

        return $this->render('carslist.html.twig', [
            'cars' => $cars,
        ]);
    }

    #[Route('/cars/{id}/details', name: 'app_car_details')]
    public function detailscar(Car $car)
    {
         // Récupérer l'ouvrier via son ID
        return $this->render('cardetails.html.twig', [
            'car' => $car,]);
        
        // Vérifier si l'ouvrier existe
        if (!$car) {
            throw $this->createNotFoundException('L\'car n\'existe pas.');
        }

        // Retourner la vue avec les détails de l'ouvrier
            return $this->render('car/cardetails.html.twig', [
                'car' => $car,
            ]);
    }

    #[Route('/cars/{id}/delete', name: 'app_car_delete')]
    public function deletecar(Car $car, EntityManagerInterface $entityManager): Response
    {  
        $rentals = $entityManager->getRepository(Rental::class)->findBy(['car' => $car]);

        // Supprimer les locations associées
        foreach ($rentals as $rental) {
            $entityManager->remove($rental);  // Supprimer chaque location associée
        }

        $entityManager->remove($car);
        $entityManager->flush();

        // Rediriger vers la liste des clients
        return $this->redirectToRoute('app_cars');
    }
    #[Route('/car/add', name: 'app_car_add', methods:['GET'])]
    public function addCar(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll(); // Récupère toutes les catégories
        return $this->render('caradd.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/car/create', name: 'app_car_create', methods:['POST'])]
    public function createcar( CategoryRepository $categoryRepository,Request $request, EntityManagerInterface $entityManager):Response{
       
       $model=$request->request->get("model");
       $brand=$request->request->get("brand");
       $year=$request->request->get("priceperday");
       $priceperday=$request->request->get("priceperday");
       $category_id=$request->request->get("category_id");
       $category = $categoryRepository->find($category_id);

       if (!$category) {
           throw $this->createNotFoundException('Category not found');
       }
       if (!$model || !$brand || !$year || !$priceperday || !$category_id) {
        $this->addFlash('error', 'All fields are required!');
        return $this->redirectToRoute('app_car_create_form'); // Redirection vers le formulaire
    }
    $car=new Car();
    $car->setBrand($brand);
    $car->setModel($model);
    $car->setCategory($category);
    $car->setPriceperday($priceperday);
    $car->setYear($year);                 
    $entityManager->persist($car);
    $entityManager->flush();
      
        return $this->redirectToRoute('app_cars'); // Redirection vers la liste des catégories
    }
    #[Route('/car/edit/{id}', name: 'app_car_edit')]
    public function editCar(int $id, Request $request, CarRepository $carRepository, EntityManagerInterface $entityManager): Response
    {
    $car = $carRepository->find($id);

    if (!$car) {
        throw $this->createNotFoundException('Car not found');
    }

    if ($request->isMethod('POST')) {
        $car->setBrand($request->request->get('brand'));
        $car->setModel($request->request->get('model'));
        $car->setYear($request->request->get('year'));
        $car->setPriceperday($request->request->get('priceperday'));
        $categoryId = $request->request->get('category_id');

        if ($categoryId) {
            $category = $entityManager->getRepository(Category::class)->find($categoryId);
            if ($category) {
                $car->setCategory($category);
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Car updated successfully!');
        return $this->redirectToRoute('app_cars'); // Retourner à la liste des voitures
    }

    $categories = $entityManager->getRepository(Category::class)->findAll();

    return $this->render('caredit.html.twig', [
        'car' => $car,
        'categories' => $categories,
    ]);
}

    #[Route('/car/new_form', name: 'app_car_new_form')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de l'entité car
        $car = new Car();

        // Créer le formulaire pour l'entité car
        $form = $this->createForm(carFormType::class, $car);

        // Traiter la requête (si le formulaire a été soumis)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'entité car dans la base de données
            $entityManager->persist($car);
            $entityManager->flush();

            // Ajouter un message flash pour notifier que l'entité a été enregistrée
            $this->addFlash('success', 'car created successfully!');

            // Rediriger vers la liste des catégories (ou une autre page)
            return $this->redirectToRoute('app_cars');
        }

        // Rendre la vue avec le formulaire
        return $this->render('new_form_car.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
