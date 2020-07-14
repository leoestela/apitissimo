<?php


namespace App\Service;


use App\Exception\Common\ConstraintErrorException;
use App\Exception\Common\RequiredFieldMissingException;
use Exception;
use Symfony\Component\Validator\Validation;

class ValidationService
{
    /**
     * @param string $requiredField
     * @throws Exception
     */
    protected function requiredFieldInformed(string $requiredField)
    {
        if (null == $requiredField)
        {
            throw RequiredFieldMissingException::throwException();
        }
    }

    /**
     * @param $object
     * @throws Exception
     */
    public function constraintsValidation($object)
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

        $violations = $validator->validate($object);

        if (count($violations) >0)
        {
            throw ConstraintErrorException::withError($violations[0]->getMessage());
        }
    }
}