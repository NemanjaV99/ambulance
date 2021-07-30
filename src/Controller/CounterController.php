<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CounterController extends AbstractController
{
    /**
     * @Route("/counter", name="counter_home")
     */
    public function index(): Response
    {
        return $this->render('counter/index.html.twig', [
            'test' => 'Counter',
        ]);
    }
}
