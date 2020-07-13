<?php


namespace App\Service;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            throw new Exception('Required field not informed', JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $email
     * @throws Exception
     */
    protected function isValidEmail(string $email)
    {
        if (false == filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new Exception('Invalid e-mail', JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $email
     * @param int $phone
     * @param string $address
     * @throws Exception
     */
    protected function userValidData(string $email, int $phone, string $address)
    {
        $this->requiredFieldInformed($email);
        $this->requiredFieldInformed($phone);
        $this->requiredFieldInformed($address);

        $this->isValidEmail($email);
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
            throw new Exception($violations[0]->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}