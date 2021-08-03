<?php

namespace App\Validation\Constraints;

use App\Repository\PatientRepository;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validation\Constraints\UniqueInDatabase;
use UnexpectedValueException;

class UniqueInDatabaseValidator extends ConstraintValidator
{
    private $patientRepository;

    public function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueInDatabase) {
            throw new UnexpectedTypeException($constraint, UniqueInDatabase::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        // The class will for now handle the validation of JMBG number 

        // Attempt to find a patient using the passed jmbg value
        $patient = $this->patientRepository->findByJmbg($value);

        if ($patient) {

            // If a patient with given JMBG number was found, the value is not unique, so this constraint fails
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

    }
}
