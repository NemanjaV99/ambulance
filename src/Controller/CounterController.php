<?php

namespace App\Controller;

use App\Repository\ExaminationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CounterController extends AbstractController
{
    /**
     * @Route("/counter", name="counter_home")
     */
    public function index(ExaminationRepository $examinationRepository): Response
    {
        $examinations = $examinationRepository->findAllJoinedToDoctorAndPatient();

        // Separate the examinations on performed and not performed, for easier disaply in template
        // If no examinations exist, empty arrays will be returned
        $performedExaminations = [];
        $waitingExaminations = [];

        if (!empty($examinations)) {
            
            foreach ($examinations as $examination) {

                if ($examination['performed']) {
                    array_push($performedExaminations, $examination);
                } else {
                    array_push($waitingExaminations, $examination);
                }
            }

        }

        return $this->render('counter/index.html.twig', [
            'performed_examinations' => $performedExaminations,
            'waiting_examinations' => $waitingExaminations
        ]);
    }
}
