<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InterfacesTraitementController extends AbstractController
{
    #[Route('/interfaces/traitement', name: 'app_interfaces_traitement')]
    public function index(): Response
    {
        return $this->render('interfaces_traitement/index.html.twig', [
            'controller_name' => 'InterfacesTraitementController',
        ]);
    }
    #[Route('/principale', name: 'app_principale')]
    public function principale(): Response
    {
        return $this->render('principale.html.twig', [
            'controller_name' => 'RentalsTraitementController',
        ]);
    }
    
}
