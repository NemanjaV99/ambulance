<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Examination;
use App\Entity\Doctor;
use App\Form\ExaminationType;

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

    /**
     * @Route("/examination/delete", name="examination_delete", methods={"POST"})
     */
    public function delete(Request $request)
    {
        $status = false;

        if ($request->request->has('identity') && is_numeric($request->request->get('identity'))) {

            $examinationId = $request->request->get('identity');

            $entityManager = $this->getDoctrine()->getManager();
            $examination = $entityManager->getRepository(Examination::class)->find($examinationId);

            // If examination with the given id was not found, success flag will stay false, meaning error will be returned
            if ($examination) {

                $currentUser = $this->getUser();

                if ($currentUser->isRole('ROLE_COUNTER')) {

                    // Then we don't need to check anything, counter user can delete any examinations
                    $entityManager->remove($examination);
                    $entityManager->flush();

                    $status = true;

                } else if ($currentUser->isRole('ROLE_DOCTOR')) {

                    // We will allow doctor users to only delete their own examinations. To prevent doctors users from deleting other doctor's examinations
                    // Using the current user id, retrieve doctor object
                    $currentDoctor = $entityManager->getRepository(Doctor::class)->findByUserIdJoinedToUserAndType($currentUser->getId());
                    $examinationDoctor = $examination->getDoctor();

                    if ($currentDoctor['id'] === $examinationDoctor->getId()) {

                        // If the examination doctor is the doctor who requsted the delete, then we will allow him to delete
                        $entityManager->remove($examination);
                        $entityManager->flush();

                        $status = true;
                    }

                    
                }
            }

        }

        if ($status) {
            $request->getSession()->set('examination_delete_success', 'Successfully deleted.');
        } else {
            $request->getSession()->set('examination_delete_error', 'Something went wrong. Failed to delete doctor.');
        }

        return $this->redirectToRoute('counter_home');
    }
}
