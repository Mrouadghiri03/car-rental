<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Category;
use App\Entity\Car;
use App\Entity\Customer;
use App\Entity\Rental;
use App\Repository\CustomerRepository;
use App\Repository\CarRepository;
use App\Repository\CategoryRepository;
use App\Repository\RentalRepository;


use Doctrine\ORM\EntityManagerInterface;

class InstancierEntitiesController extends AbstractController
{
    #[Route('/instancier/entities', name: 'app_instancier_entities')]
    public function index(): Response
    {
        return $this->render('instancier_entities/index.html.twig', [
            'controller_name' => 'InstancierEntitiesController',
        ]);
    }

    #[Route('/create/cars_categories_clients', name: 'create_cars_categories_clients')]
    public function createCategoryAndCar(EntityManagerInterface $entityManager): JsonResponse
    {
        // Création des catégories
        $category1 = new Category();
        $category1->setName('SUV');
        $category1->setDescription("L3lo");

        $category2 = new Category();
        $category2->setName('Sedan');
        $category2->setDescription("lhabt");


        // Persistance des catégories
        $entityManager->persist($category1);
        $entityManager->persist($category2);

        // Création des voitures
        $car1 = new Car();
        $car1->setBrand('Mercedes');
        $car1->setModel('w 123');
        $car1->setYear(1976);
        $car1->setPriceperday(500);
        $car1->setCategory($category2); // Lier la voiture à la catégorie 'SUV'

        $car2 = new Car();
        $car2->setBrand('Volgswagen');
        $car2->setModel('golf4');
        $car2->setYear(2001);
        $car2->setPriceperday(200);
        $car2->setCategory($category2); // Lier la voiture à la catégorie 'Sedan'

        // Persistance des voitures
        $entityManager->persist($car1);
        $entityManager->persist($car2);

        // Création de clients
        $customer1 = new Customer();
        $customer1->setName("Hassan");
        $customer1->setEmail("chhassan@gmail.com");
        $customer1->setAddress("hay al laymoun, berkane");
        $customer1->setLicencenumber("FA767649");
        $customer1->setPhone("+2126547200");

        $customer2 = new Customer();
        $customer2->setName("Brahim");
        $customer2->setEmail("brhimouadghiri61@gmail.com");
        $customer2->setAddress("76 rue oujda, madagh");
        $customer2->setLicencenumber("FE4949");
        $customer2->setPhone("+21268534373");

        // Persistance des clients
        $entityManager->persist($customer1);
        $entityManager->persist($customer2);
        //ajouter des emprunt
        $rental1=new Rental();
        $rental1->setCustomer($customer2);
        $rental1->setCar($car2);
        $rental1->setRentaldate(new \DateTime('2024-12-15'));
        $rental1->setReturndate(new \DateTime('2024-12-20'));
        $rental1->setActuelreturndate(new \DateTime('2024-12-19'));
        if($rental1->getActuelreturndate() > $rental1->getReturndate()){
            $rental1->setlate(true);
        }
        else{
            $rental1->setLate(false);
        }

        $duree=$rental1->getRentaldate()->diff($rental1->getReturndate());
        $dureedys=$duree->days;
        $rental1->setTotalcost($car1->getPriceperday() * $dureedys);
        $entityManager->persist($rental1);
        $rental2=new Rental();
        $rental2->setCustomer($customer1);
        $rental2->setCar($car1);
        $rental2->setRentaldate(new \DateTime('2024-12-14'));
        $rental2->setReturndate(new \DateTime('2024-12-21'));
        $rental2->setActuelreturndate(new \DateTime('2024-12-21'));
        if($rental2->getActuelreturndate() > $rental2->getReturndate()){
            $rental2->setlate(true);
        }
        else{
            $rental2->setLate(false);
        }

        $duree=$rental2->getRentaldate()->diff($rental2->getReturndate());
        $dureedys=$duree->days;
        $rental2->setTotalcost($car1->getPriceperday() * $dureedys);
        $entityManager->persist($rental2);
        // Flushing pour enregistrer dans la base de données
        $entityManager->flush();

        // Retourner une réponse JSON avec succès
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Categories, cars, and customers created successfully!'
        ]);
    }
    /*
    #[Route('/principale', name: 'app_principale')]
    public function principale(): Response
    {
        return $this->render('principale.html.twig', [
            'controller_name' => 'RentalsTraitementController',
        ]);
    }
    */
    /*
    #[Route('/customers', name: 'app_customer')]
    public function listcustomer(CustomerRepository $customer): Response
    {
        // Récupérer tous les clients
        $customers = $customer->findAll();
    
        return $this->render('listecustomer.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/customer/{id}/details', name: 'app_customer_details')]
    public function details(Customer $customer){
         // Récupérer l'ouvrier via son ID
         return $this->render('details.html.twig', [
            'customer' => $customer,]);

    // Vérifier si l'ouvrier existe
    if (!$customer) {
        throw $this->createNotFoundException('L\'ouvrier n\'existe pas.');
    }

    // Retourner la vue avec les détails de l'ouvrier
    return $this->render('customer/details.html.twig', [
        'customer' => $customer,
    ]);
    }
    #[Route('/customers/{id}/delete', name: 'app_customer_delete')]
public function delete(Customer $customer, EntityManagerInterface $entityManager): Response
{
    // Supprimer les locations associées au client
    $rentals = $entityManager->getRepository(Rental::class)->findBy(['customer' => $customer]);
    
    foreach ($rentals as $rental) {
        $entityManager->remove($rental);  // Supprimer chaque location associée
    }

    // Ensuite, supprimer le client
    $entityManager->remove($customer);
    $entityManager->flush();

    // Rediriger vers la liste des clients
    return $this->redirectToRoute('app_customer');
}
*/
/*
#[Route('/cars', name: 'app_cars')]
public function listcars(CarRepository $car): Response
{
    // Récupérer tous les clients
    $cars = $car->findAll();

    return $this->render('listecar.html.twig', [
        'cars' => $cars,
    ]);
}

#[Route('/cars/{id}/details', name: 'app_car_details')]
public function detailscar(Car $car){
     // Récupérer l'ouvrier via son ID
     return $this->render('detailscar.html.twig', [
        'car' => $car,]);

// Vérifier si l'ouvrier existe
if (!$car) {
    throw $this->createNotFoundException('L\'ouvrier n\'existe pas.');
}

// Retourner la vue avec les détails de l'ouvrier
return $this->render('car/detailscar.html.twig', [
    'car' => $car,
]);
}
#[Route('/cars/{id}/delete', name: 'app_car_delete')]
public function deletecar(Car $car, EntityManagerInterface $entityManager): Response
{  $rentals = $entityManager->getRepository(Rental::class)->findBy(['car' => $car]);

    // Supprimer les locations associées
    foreach ($rentals as $rental) {
        $entityManager->remove($rental);  // Supprimer chaque location associée
    }

$entityManager->remove($car);
$entityManager->flush();

// Rediriger vers la liste des clients
return $this->redirectToRoute('app_cars');
}
*/
/*
#[Route('/categories', name: 'app_categories')]
public function listcategory(CategoryRepository $category): Response
{
    // Récupérer tous les clients
    $categories = $category->findAll();

    return $this->render('listecategories.html.twig', [
        'categories' => $categories,
    ]);
}


#[Route('/categories/{id}/delete', name: 'app_category_delete')]
public function deletecategory(Category $category, EntityManagerInterface $entityManager): Response
{  $cars = $entityManager->getRepository(Car::class)->findBy(['category' => $category]);

    // Supprimer les locations associées
    foreach ($cars as $car) {
        $entityManager->remove($car);  // Supprimer chaque location associée
    }

$entityManager->remove($category);
$entityManager->flush();

// Rediriger vers la liste des clients
return $this->redirectToRoute('app_categories');
}
*/
/*
#[Route('/rentals', name: 'app_rentals')]
public function listrentals(RentalRepository $rental): Response
{
    // Récupérer tous les clients
    $rentals = $rental->findAll();

    return $this->render('listerentals.html.twig', [
        'rentals' => $rentals,
    ]);
}

#[Route('/rentals/{id}/details', name: 'app_rentals_details')]
public function detailsrental(Rental $rental){
     // Récupérer l'ouvrier via son ID
     return $this->render('detailsrental.html.twig', [
        'rental' => $rental,]);

// Vérifier si l'ouvrier existe
if (!$rental) {
    throw $this->createNotFoundException('L\'ouvrier n\'existe pas.');
}

// Retourner la vue avec les détails de l'ouvrier
return $this->render('rental/detailsrental.html.twig', [
    'rental' => $rental,
]);
}


#[Route('/rentalss/{id}/delete', name: 'app_rentals_delete')]
public function deleterentals(Rental $rental, EntityManagerInterface $entityManager): Response
{ 
$entityManager->remove($rental);
$entityManager->flush();

// Rediriger vers la liste des clients
return $this->redirectToRoute('app_rentals');
}

   */ 
}
