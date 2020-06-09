<?php


namespace App\Service;


use Exception;

class UserService
{
    /**
     * @param string $email
     * @param string $phone
     * @param string $address
     * @throws Exception
     */
    public function actualizeUser(string $email, string $phone, string $address): void
    {
        if (!$this->allRequiredFieldsArePresent($email, $phone, $address))
        {
            throw new Exception('Required field not informed', 100);
        }

        if (!$this->isValidEmail($email))
        {
            throw new Exception('Invalid e-mail', 100);
        }
    }

    private function allRequiredFieldsArePresent(string $email, string $phone, string $address):bool
    {
        $allRequiredFieldArePresent = true;

        if (null == $email || null == $phone || null == $address)
        {
            $allRequiredFieldArePresent = false;
        }

        return $allRequiredFieldArePresent;
    }

    private function isValidEmail(string $email):bool
    {
        return (false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}