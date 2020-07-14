<?php


namespace App\Exception\Common;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class InvalidJsonException extends Exception
{
    private const MESSAGE = 'Invalid json body';

    /**
     * @return static
     * @throws InvalidJsonException
     */
    public static function throwException(): self
    {
        throw new self(self::MESSAGE, JsonResponse::HTTP_BAD_REQUEST);
    }
}