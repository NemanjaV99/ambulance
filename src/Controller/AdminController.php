<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DoctorRepository;
use App\Repository\PatientRepository;

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
        // Add pagination later
        $doctors = $doctorRepository->findAllJoinedToTypeAndUser();

        return $this->render('admin/doctor/list.html.twig', ['doctors' => $doctors]);
    }

    /**
     * @Route("/admin/patients", name="admin_patient_list")
     */
    public function listPatients(PatientRepository $patientRepository): Response
    {
        // Add pagination later
        $patients = $patientRepository->findAllJoinedToLocation();

        // Since we don't want to show entire note content in table, go through the retireved patients and for each shorten the note string
        foreach ($patients as &$patient) {

            if ($patient['note'] === null) {
                continue;
            }

            $patient['note'] = substr($patient['note'], 0, 25);
            $patient['note'] .= '...';
        }


        return $this->render('admin/patient/list.html.twig', ['patients' => $patients]);
    }
}
