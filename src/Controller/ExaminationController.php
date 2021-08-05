<?php

namespace App\Controller;

use App\Entity\Examination;
use App\Form\ExaminationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExaminationController extends AbstractController
{
    /**
     * @Route("/examination", name="examination_create")
     */
    public function create(Request $request)
    {
         // just setup a fresh $task object (remove the example data)
         $examination = new Examination();

         $creatExaminationForm = $this->createForm(ExaminationType::class, $examination, ['validation_groups' => 'create_examination']);
 
         $creatExaminationForm->handleRequest($request);

         if ($creatExaminationForm->isSubmitted() && $creatExaminationForm->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();

            // Set default value
            $examination->setPerformed(0);

            $entityManager->persist($examination);
            $entityManager->flush();

            $request->getSession()->set('examination_create_success', 'Successfully booked a new examination.');

            return $this->redirectToRoute('counter_home');
        
         }
 
         return $this->render('examination/create.html.twig', [
             'create_examination_form' => $creatExaminationForm->createView(),
         ]);
    }
}
