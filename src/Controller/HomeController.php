<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        /**
         *  If user is logged in 
         *  {
         *      #Check user type
         * 
         *      If user type === 'doctor'
         * 
         *          { show doctors section }
         * 
         *      Else if user type === 'admin'
         * 
         *          { show admins section }
         *
         *  }
         * 
         *  If user is not logged in
         *  {
         *      show him login form
         *  }
         * 
         */
            
         return $this->render('index.html.twig', ['test' => 'Hello World!']);
    }
}
