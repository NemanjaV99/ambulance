<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Examination;
use App\Entity\Doctor;
use App\Form\ExaminationType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

                    // We will allow doctor users to only delete their own examinations. To prevent doctor users from deleting other doctor's examinations
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


    /**
     * @Route("/examination/{examinationId}/update", name="examination_update", requirements={"examinationId"="\d+"})
     */
    public function update(int $examinationId, Request $request)
    {
        $response = [];

        // Find a patient with the given id
        $entityManager = $this->getDoctrine()->getManager();
        $examination = $entityManager->getRepository(Examination::class)->find($examinationId);

        if (!$examination) {

            throw $this->createNotFoundException('Examination with given id was not found.');
        }

        $currentUser = $this->getUser();

        if ($currentUser->isRole('ROLE_DOCTOR')) {

            $currentDoctor = $entityManager->getRepository(Doctor::class)->findByUserIdJoinedToUserAndType($currentUser->getId());
            $examinationDoctor = $examination->getDoctor();

            if ($currentDoctor['id'] !== $examinationDoctor->getId()) {

                throw new AccessDeniedHttpException('You are not authorized to access this information.');
            }
            
            // Otherwise, if the id's match, doctor is trying to update his own examination (examination assigned to him), allow him
            // Doctors can only update diagnosis and performed field
            $updateExaminationForm = $this->createForm(ExaminationType::class, $examination, [
                'validation_groups' => 'update_by_doctor',
                
            ]);

        } else {

            // User has ROLE_COUNTER, since these two roles are only ones allowed to access these routes (^/examination)
            $updateExaminationForm = $this->createForm(ExaminationType::class, $examination, [
                'validation_groups' => 'update_by_counter'
            ]);
        
        }

        if ($request->isMethod('POST')) {

            // Submitting manually prevents symfony from trying to set each field even if we are not trying to update all of them
            // That is because of the manual submit with this method, and second bool argument that removes empty/missing entity fields from request
            // handleRequest() tries to map all fields on an entity even if we don't set them in the form/request
            $updateExaminationForm->submit($request->request->get($updateExaminationForm->getName()), false);
        }

        if ($updateExaminationForm->isSubmitted() && $updateExaminationForm->isValid()) {

            // If the examination has already been marked as performed, and user tries unchecking it ( marking it as not performed again ), 
            // the checkbox will not be submitted, and entity won't get updated. We need to do this manually
            // Check following: If entity has performed set to true (this is data from db), and the checkbox field is not submitted in the request
            // that can only mean that user wants to uncheck it, mark it as 'not performed'
            // Otherwise if he does not touch it, then it will be true, and submitted
            if ($examination->getPerformed() && ! $updateExaminationForm->get('performed')->isSubmitted()) {

                // Then set performed to false, otherwise leave as is
                $examination->setPerformed(false);
            }

            // Update the entity
            $entityManager->flush();

            $response['notice'] = 'Successfully updated.';
        }

        $response['update_examination_form'] = $updateExaminationForm->createView();

        return $this->render('examination/update.html.twig', $response);

    }
}
