<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * 
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // If the above code passes, we need to redirect the user based on his role

        $route = '';
        
        if ($this->isGranted('ROLE_ADMIN')) {

            $route = 'admin_home';

        } else if ($this->isGranted('ROLE_DOCTOR')) {

            $route = 'doctor_home';

        } else if ($this->isGranted('ROLE_COUNTER')) {

            $route = 'counter_home';

        }

        return $this->redirectToRoute($route);
    }
}
