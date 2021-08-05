<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use App\Repository\ExaminationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CounterController extends AbstractController
{
    /**
     * @Route("/counter", name="counter_home")
     */
    public function index(ExaminationRepository $examinationRepository, DoctorRepository $doctorRepository, Request $request): Response
    {
        $response = [];

        $examinations = $examinationRepository->findAllJoinedToDoctorAndPatient();

        $currentUser = $this->getUser();
        
        // Only if the current user is doctor, then we need to retrieve the doctor object to check if he can update/delete examinations, since 
        // doctors can only delete/update their own examinations
        if ($currentUser->isRole('ROLE_DOCTOR')) {
            $currentDoctor = $doctorRepository->findByUserIdJoinedToUserAndType($currentUser->getId());
        }

        // Separate the examinations on performed and not performed, for easier disaply in template
        // If no examinations exist, empty arrays will be returned
        $response['performed_examinations'] = [];
        $response['waiting_examinations'] = [];

        if (!empty($examinations)) {

            foreach ($examinations as $examination) {

                if ($currentUser->isRole('ROLE_DOCTOR')) {

                    // Doctors can only edit/delete their own examinations
                    // Check that, and set a flag based on which we will display the edit/delete actions on the template
                    if ($currentDoctor['id'] === $examination['doctor_id']) {
                        $examination['actions_allowed'] = true;
                    } else {
                        $examination['actions_allowed'] = false;
                    }

                } else {

                    // Users with counter role can update/delete all examinations
                    $examination['actions_allowed'] = true;
                }

                if ($examination['performed']) {
                    array_push($response['performed_examinations'], $examination);
                } else {
                    array_push($response['waiting_examinations'], $examination);
                }
            }

        }

        if ($request->getSession()->has('examination_create_success')) {

            $response['notice'] = $request->getSession()->get('examination_create_success');
            $request->getSession()->remove('examination_create_success');

        }
        
        // There should not be a chance for user to get both the create_success and delete_success notice in one request
        // Meaning that they should not overwrite
        if ($request->getSession()->has('examination_delete_success')) {

            $response['notice'] = $request->getSession()->get('examination_delete_success');
            $request->getSession()->remove('examination_delete_success');

        } else if ($request->getSession()->has('examination_delete_error')) {

            $response['error'] = $request->getSession()->get('examination_delete_error');
            $request->getSession()->remove('examination_delete_error');
        }

        return $this->render('counter/index.html.twig', $response);
    }
}
