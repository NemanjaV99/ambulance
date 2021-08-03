<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueInDatabase extends Constraint
{
    public $message = 'The value you entered already exists.';
}
