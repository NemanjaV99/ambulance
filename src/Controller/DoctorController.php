<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Examination;
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
        $currentUser = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $currentDoctor = $entityManager->getRepository(Doctor::class)->findByUserIdJoinedToUserAndType($currentUser->getId());

        // Find all not performed examinations for current logged in doctor user
        $examinations = $entityManager->getRepository(Examination::class)->findAllIncompleteByDoctorIdJoinedToDoctorAndPatient($currentDoctor['id']);

        return $this->render('doctor/index.html.twig', ['examinations' => $examinations]);
    }
}
