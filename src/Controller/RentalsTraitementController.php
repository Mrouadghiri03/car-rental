<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Car;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RentalRepository;
use App\Repository\CarRepository;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RentalFormType;
use App\Entity\Rental;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RentalsTraitementController extends AbstractController
{
    #[Route('/rentals/traitement', name: 'app_rentals_traitement')]
    public function index(): Response
    {
        return $this->render('rentals_traitement/index.html.twig', [
            'controller_name' => 'RentalsTraitementController',
        ]);
    }
    #[Route('/rentals', name: 'app_rentals')]
    public function listrentals(RentalRepository $rental): Response
    {
        // Récupérer tous les clients
        $rentals = $rental->findAll();

        return $this->render('rentalslist.html.twig', [
            'rentals' => $rentals,
        ]);
    }

    #[Route('/rentals/{id}/details', name: 'app_rentals_details')]
    public function detailsrental(Rental $rental)
    {
        // Récupérer l'ouvrier via son ID
        return $this->render('rentaldetails.html.twig', [
            'rental' => $rental,]);

        // Vérifier si l'ouvrier existe
        if (!$rental) {
            throw $this->createNotFoundException('L\'rental n\'existe pas.');
        }

        // Retourner la vue avec les détails de l'ouvrier
        return $this->render('rental/rentaldetails.html.twig', [
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
    #[Route('/rental/add', name: 'app_rental_add', methods:['GET'])]
    public function addCar(CarRepository $carRepository,CustomerRepository $customerRepository): Response
    {   
        $cars=$carRepository->findAll();
        $customers=$customerRepository->findAll();
        return $this->render('rentaladd.html.twig', [
       'cars'=>$cars,'customers'=>$customers
        ]);
    }
    #[Route('/rental/create', name: 'app_rental_create', methods:['POST'])]
    public function createcar( CarRepository $carRepository,CustomerRepository $customerRepository,Request $request, EntityManagerInterface $entityManager ):Response{
        $rentaldate=$request->request->get("rental_date");
        $returndate=$request->request->get("return_date");
        $car_id=$request->request->get("car_id");
        $car = $carRepository->find($car_id);
        $customer_id=$request->request->get("customer_id");
        $customer=$customerRepository->find($customer_id);
        if (!$car) {
            throw $this->createNotFoundException('car not found');
        }
        if (!$customer) {
            throw $this->createNotFoundException('customer not found');
        }
        $rental=new Rental();
        $rental->setRentaldate(new DateTime($rentaldate));
        $rental->setReturndate(new DateTime($returndate));
        $rental->setActuelreturndate(new DateTime()); // Affiche la date et l'heure actuelle
     
        if($rental->getActuelreturndate() > $rental->getReturndate()){
            $rental->setlate(true);
            $interval_total = $rental->getActuelreturndate()->diff($rental->getReturndate());
            $interval_initial = $rental->getReturndate()->diff($rental->getRentaldate());

            // Obtenir le nombre de jours de la différence
            $dayslate = $interval_total->days;
            $daysinitial=$interval_initial->days;
         //   $days_late=$daystotal-$daysinitial;
            $rental->setLate(true);
            $totalCost = ($dayslate * ($car->getPriceperday() + 50)) + ($daysinitial * $car->getPriceperday());

            $rental->setTotalcost($totalCost);

        }
        else{
            $rental->setLate(false);
            $interval_initial = $rental->getReturndate()->diff($rental->getRentaldate());
            $daysinitial=$interval_initial->days;
            $rental->setLate(false);
            $rental->setTotalcost(($daysinitial*$car->getPriceperday()));


        }
        $rental->setCar($car);
        $rental->setCustomer($customer);
        $entityManager->persist($rental);
        $entityManager->flush();
        return $this->redirectToRoute("app_rentals");
    }
       
    #[Route('/rental/edit/{id}', name: 'app_rentals_edit')]
    public function editCar(int $id, Request $request, RentalRepository $rentalRepository, EntityManagerInterface $entityManager): Response
    {
        $rental = $rentalRepository->find($id);
    
        if (!$rental) {
            throw $this->createNotFoundException('Rental not found');
        }

        if ($request->isMethod('POST')) {
            $rental->setRentaldate($request->request->get('rental_date'));
            $rental->setReturndate($request->request->get('return_date'));
            $carId = $request->request->get('car_id');
            $customerId=$request->request->get('customer_id');
    
            if ($customerId) {
                $customer = $entityManager->getRepository(Customer::class)->find($customerId);
                if ($customer) {
                    $customer->setCustomer($customer);
                }
            }
            if ($carId) {
                $car = $entityManager->getRepository(Car::class)->find($carId);
                if ($car) {
                    $car->setCar($car);
                }
            }
 
            $entityManager->flush();
    
            $this->addFlash('success', 'Rental updated successfully!');
            return $this->redirectToRoute('app_rentals'); // Retourner à la liste des voitures
        }
    
        $cars = $entityManager->getRepository(Car::class)->findAll();
        $customers=$entityManager->getRepository(Customer::class)->findAll();
    
        return $this->render('rentaledit.html.twig', [
            'rental' => $rental,
            'cars' => $cars,
            'customers' => $customers,

        ]);
    }
    
    #[Route('/rental/new_form', name: 'app_rental_new_form')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rental = new Rental();

        // Créer le formulaire de location
        $form = $this->createForm(RentalFormType::class, $rental);
    
        // Si le formulaire est soumis et valide
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $rental = $form->getData();
            $rental->setActuelreturndate(new DateTime());
            // Calculer la durée de la location et le retard
            $currentReturnDate = new \DateTime(); // La date actuelle
            $returnDate = $rental->getReturnDate();
            $rentalDate = $rental->getRentalDate();
        

            if ($currentReturnDate > $returnDate) {
                // Calcul du retard
                $rental->setLate(true);
                $intervalTotal = $currentReturnDate->diff($returnDate);
                $intervalInitial = $returnDate->diff($rentalDate);

                $daysLate = $intervalTotal->days;
                $daysInitial = $intervalInitial->days;

                // Calcul du coût total avec supplément pour les jours de retard
                $car = $rental->getCar();
                $totalCost = ($daysLate * ($car->getPriceperday() + 50)) + ($daysInitial * $car->getPriceperday());
                $rental->setTotalcost($totalCost);
            } 
            else {
                // Pas de retard, juste le coût de la location initiale
                $rental->setLate(false);
                $intervalInitial = $returnDate->diff($rentalDate);
                $daysInitial = $intervalInitial->days;

                $car = $rental->getCar();
                $rental->setTotalcost($daysInitial * $car->getPriceperday());
            }

            // Persister l'entité Rental
            $entityManager->persist($rental);
            $entityManager->flush();

            // Rediriger vers la liste des locations après la création
            return $this->redirectToRoute('app_rentals');
        }

        // Rendre la vue avec le formulaire
        return $this->render('new_form_rental.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
    
