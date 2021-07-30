<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DoctorController extends AbstractController
{
    /**
     * @Route("/doctor", name="doctor_home")
     */
    public function index(): Response
    {
        return $this->render('doctor/index.html.twig', [
            'test' => 'Doctor',
        ]);
    }
}
