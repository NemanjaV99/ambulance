<?php

namespace App\Controller;

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
    public function index(ExaminationRepository $examinationRepository, Request $request): Response
    {
        $response = [];

        $examinations = $examinationRepository->findAllJoinedToDoctorAndPatient();

        // Separate the examinations on performed and not performed, for easier disaply in template
        // If no examinations exist, empty arrays will be returned
        $response['performed_examinations'] = [];
        $response['waiting_examinations'] = [];

        if (!empty($examinations)) {
            
            foreach ($examinations as $examination) {

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

        return $this->render('counter/index.html.twig', $response);
    }
}
