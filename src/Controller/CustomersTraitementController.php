<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Rental;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class CustomersTraitementController extends AbstractController
{
    #[Route('/customers/traitement', name: 'app_customers_traitement')]
    public function index(): Response
    {
        return $this->render('customers_traitement/index.html.twig', [
            'controller_name' => 'CustomersTraitementController',
        ]);
    }
    #[Route('/customers', name: 'app_customers')]
    public function listcustomer(CustomerRepository $customer): Response
    {
        // Récupérer tous les clients
        $customers = $customer->findAll();
    
        return $this->render('customerslist.html.twig', [
            'customers' => $customers,
        ]);
    }
    
    #[Route('/customer/{id}/details', name: 'app_customer_details')]
    public function details(Customer $customer){
         // Récupérer l'ouvrier via son ID
         return $this->render('customerdetails.html.twig', [
            'customer' => $customer,]);

    // Vérifier si l'ouvrier existe
    if (!$customer) {
        throw $this->createNotFoundException('L\'customer n\'existe pas.');
    }

    // Retourner la vue avec les détails de l'ouvrier
    return $this->render('customer/customerdetails.html.twig', [
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
        return $this->redirectToRoute('app_customers');
    }
    #[Route('/customer/add', name: 'app_customer_add', methods: ['GET'])]
    public function addCustomer(): Response
    {
        return $this->render('customeradd.html.twig'); // Affiche le fichier Twig correspondant
    }
    #[Route("/customer/create", name:"app_customer_create", methods:["POST"])]
    public function createCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $phone=$request->request->get('phone');
        $licenecenumber=$request->request->get('licencenumber');
        $address=$request->request->get('address');
    
        if (!$name || !$email || !$phone || !$licenecenumber || !$address) {
            $this->addFlash('error', 'All fields are required!');
            return $this->redirectToRoute('app_customer_create_form'); // Redirection vers le formulaire
        }
    
     $customer=new Customer();

    $customer->setName($name);
    $customer->setEmail($email);
    $customer->setPhone($phone);
    $customer->setLicencenumber($licenecenumber);
    $customer->setAddress($address);
        $entityManager->persist($customer);
        $entityManager->flush();
    
        $this->addFlash('success', 'Customer successfully created!');
        return $this->redirectToRoute('app_customers'); // Redirection vers la liste des catégories
    }
    

    #[Route('/customer/edit/{id}', name: 'app_customer_edit')]
public function editCar(int $id, Request $request, CustomerRepository $customerRepository, EntityManagerInterface $entityManager): Response
{
    $customer = $customerRepository->find($id);

    if (!$customer) {
        throw $this->createNotFoundException('Customer not found');
    }

    if ($request->isMethod('POST')) {
        $customer->setName($request->request->get('name'));
        $customer->setEmail($request->request->get('email'));
        $customer->setPhone($request->request->get('phone'));
        $customer->setAddress($request->request->get('address'));
        $customer->setLicencenumber($request->request->get('liscencenumber'));


        

        $entityManager->flush();

        $this->addFlash('success', 'Customer updated successfully!');
        return $this->redirectToRoute('app_customers'); // Retourner à la liste des clients
    }

   

    return $this->render('customeredit.html.twig', [
        'customer' => $customer,
    ]);
}

}

