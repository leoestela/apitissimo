<?php


namespace App\Exception\Common;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConstraintErrorException extends Exception
{
    private const MESSAGE = 'Constraint error: %s';

    /**
     * @param $error
     * @return static
     * @throws ConstraintErrorException
     */
    public static function withError($error): self
    {
        throw new self(\sprintf(self::MESSAGE, $error), JsonResponse::HTTP_BAD_REQUEST);
    }
}