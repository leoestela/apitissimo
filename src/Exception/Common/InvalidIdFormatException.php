<?php


namespace App\Exception\Common;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class InvalidIdFormatException extends Exception
{
    private const MESSAGE = 'Param %s have an invalid format';

    /**
     * @param $value
     * @return static
     * @throws InvalidIdFormatException
     */
    public static function withValue($value): self
    {
        throw new self(\sprintf(self::MESSAGE, $value), JsonResponse::HTTP_BAD_REQUEST);
    }
}