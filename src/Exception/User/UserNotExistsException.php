<?php


namespace App\Exception\User;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserNotExistsException extends Exception
{
    private const MESSAGE = 'User with ID %s not exists';

    /**
     * @param $email
     * @return static
     * @throws UserNotExistsException
     */
    public static function withUserEmail($email): self
    {
        throw new self(\sprintf(self::MESSAGE, $email), JsonResponse::HTTP_BAD_REQUEST);
    }
}