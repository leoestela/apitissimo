<?php


namespace App\Service;


use Exception;

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
            throw new Exception('Required field not informed', 100);
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
            throw new Exception('Invalid e-mail', 100);
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
}