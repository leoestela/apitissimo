<?php


namespace App\Service;


use App\Exception\Common\ConstraintErrorException;
use Exception;
use Symfony\Component\Validator\Validation;

class ValidationService
{
    /**
     * @param $object
     * @throws Exception
     */
    public function constraintsValidation($object)
    {
        //Validate asserts included in entities classes
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

        $violations = $validator->validate($object);

        if (count($violations) >0)
        {
            throw ConstraintErrorException::withError($violations[0]->getMessage());
        }
    }
}