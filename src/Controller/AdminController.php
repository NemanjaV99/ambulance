<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Patient;
use App\Entity\Doctor;
use App\Entity\Location;
use App\Entity\User;
use App\Form\DoctorType;
use App\Form\PatientType;
use App\Repository\DoctorRepository;
use App\Repository\PatientRepository;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


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
    public function listDoctors(DoctorRepository $doctorRepository, Request $request, UserPasswordHasherInterface $passHasher): Response
    {
        $user = new User();
        $doctor = new Doctor();
        $response = [];

        $doctor->setUser($user);
        $newDoctorForm = $this->createForm(DoctorType::class, $doctor);
        $newDoctorForm->handleRequest($request);

        if ($newDoctorForm->isSubmitted() && $newDoctorForm->isValid()) {

           // Handle creating new doctor
           // Proccess the creation of a new patient
           $entityManager = $this->getDoctrine()->getManager();

           // Hash the password and set the default values for doctor user
           $user->setJoined(new DateTime());
           $user->setRoles(['ROLE_DOCTOR']);
           $user->setPassword($passHasher->hashPassword($user, $user->getPassword()));

           $entityManager->persist($user);
           $entityManager->persist($doctor);
           $entityManager->flush();

           $response['notice'] = 'Successfully created a new doctor.';
        }

        // Otherwise, handle the regular part of loading all patients 
        $response['create_doctor_form'] = $newDoctorForm->createView();

        // Add pagination later
        $doctors = $doctorRepository->findAllJoinedToTypeAndUser();

        $response['doctors'] = $doctors;

        if ($request->getSession()->has('doctor_delete_error')) {

            $response['error'] = $request->getSession()->get('doctor_delete_error');
            $request->getSession()->remove('doctor_delete_error');

        } else if ($request->getSession()->has('doctor_delete_success')) {

            $response['notice'] = $request->getSession()->get('doctor_delete_success');
            $request->getSession()->remove('doctor_delete_success');
        }

        return $this->render('admin/doctor/list.html.twig', $response);
    }

    /**
     * @Route("/admin/doctors/delete", name="admin_doctor_delete")
     */
    public function deleteDoctor(Request $request): Response
    {
        $status = false;

        if ($request->request->has('identity') && is_numeric($request->request->get('identity'))) {

            $doctorId = $request->request->get('identity');

            $entityManager = $this->getDoctrine()->getManager();
            $doctor = $entityManager->getRepository(Doctor::class)->find($doctorId);

            if ($doctor) {

                // Before removing the doctor, we also need to remove his linked user account
                $user = $doctor->getUser();

                $entityManager->remove($doctor);
                $entityManager->remove($user);
                $entityManager->flush();

                $status = true;
            }
        }

        if ($status) {
            $request->getSession()->set('doctor_delete_success', 'Successfully deleted the doctor.');
        } else {
            $request->getSession()->set('doctor_delete_error', 'Something went wrong. Failed to delete doctor.');
        }

        return $this->redirectToRoute('admin_doctor_list');
    }

    /**
     * @Route("/admin/patients", name="admin_patient_list")
     */
    public function listPatients(PatientRepository $patientRepository, Request $request): Response
    {
        $patient = new Patient();
        $response = [];

        $newPatientForm = $this->createForm(PatientType::class, $patient);
        $newPatientForm->handleRequest($request);

        if ($newPatientForm->isSubmitted() && $newPatientForm->isValid()) {

            // Proccess the creation of a new patient
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($patient);
            $entityManager->flush();

            $response['notice'] = 'Successfully created a new patient.';
        }

        // Otherwise, handle the regular part of loading all patients 
        $response['create_patient_form'] = $newPatientForm->createView();

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

        $response['patients'] = $patients;

        if ($request->getSession()->has('patient_delete_error')) {

            $response['error'] = $request->getSession()->get('patient_delete_error');
            $request->getSession()->remove('patient_delete_error');

        } else if ($request->getSession()->has('patient_delete_success')) {

            $response['notice'] = $request->getSession()->get('patient_delete_success');
            $request->getSession()->remove('patient_delete_success');
        }

        return $this->render('admin/patient/list.html.twig', $response);

    }

    /**
     * @Route("/admin/patients/{patientId}/update", name="admin_patient_update", requirements={"patientId"="\d+"})
     */
    public function updatePatient(int $patientId, Request $request)
    {
        $response = [];

        // Find a patient with the given id
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $entityManager->getRepository(Patient::class)->find($patientId);

        if (!$patient) {

            throw $this->createNotFoundException('Patient with given id was not found.');
        }

        // Otherwise patient was found, so we can populate the form with him
        $updatePatientForm = $this->createForm(PatientType::class, $patient);
        $updatePatientForm->handleRequest($request);

        if ($updatePatientForm->isSubmitted() && $updatePatientForm->isValid()) {

            $data = $request->request->all();
            $data = $data['patient'];
            
            // First find a location with the passed id
            $newLocation = $entityManager->getRepository(Location::class)->find($data['location']);

            $patient->setFirstName($data['firstName']);
            $patient->setLastName($data['lastName']);
            $patient->setJMBG($data['jmbg']);
            $patient->setNote($data['note']);
            
            if ($newLocation->getId() !== $patient->getLocation()->getId()) {

                $patient->setLocation($newLocation);
            }

            $entityManager->flush();

            $response['notice'] = 'Successfully updated the patient.';
        }

        $response['update_patient_form'] = $updatePatientForm->createView();


        return $this->render('admin/patient/update.html.twig', $response);
    }

    /**
     * @Route("/admin/patients/delete", name="admin_patient_delete")
     */
    public function deletePatient(Request $request): Response
    {
        $status = false;

        if ($request->request->has('identity') && is_numeric($request->request->get('identity'))) {

            $patientId = $request->request->get('identity');

            $entityManager = $this->getDoctrine()->getManager();
            $patient = $entityManager->getRepository(Patient::class)->find($patientId);

            if ($patient) {

                $entityManager->remove($patient);
                $entityManager->flush();

                $status = true;
            }
        }

        if ($status) {
            $request->getSession()->set('patient_delete_success', 'Successfully deleted the patient.');
        } else {
            $request->getSession()->set('patient_delete_error', 'Something went wrong. Failed to delete patient.');
        }

        return $this->redirectToRoute('admin_patient_list');
    }
}
