<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DoctorRepository;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/doctors", name="admin_doctor_list")
     */
    public function listDoctors(DoctorRepository $doctorRepository): Response
    {
        $doctors = $doctorRepository->findAllJoinedToTypeAndUser();

        return $this->render('admin/doctor/list.html.twig', ['doctors' => $doctors]);
    }

    /**
     * @Route("/admin/patients", name="admin_patient_list")
     */
    public function listPatients(): Response
    {
        return $this->render('admin/patient/list.html.twig');
    }
}
