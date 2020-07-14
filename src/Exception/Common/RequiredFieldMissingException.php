<?php


namespace App\Exception\Common;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class RequiredFieldMissingException extends Exception
{
    private const MESSAGE = 'Required field missing';

    /**
     * @return static
     * @throws RequiredFieldMissingException
     */
    public static function throwException(): self
    {
        throw new self(self::MESSAGE, JsonResponse::HTTP_BAD_REQUEST);
    }
}