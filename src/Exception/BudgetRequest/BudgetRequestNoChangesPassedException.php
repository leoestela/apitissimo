<?php


namespace App\Exception\BudgetRequest;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class BudgetRequestNoChangesPassedException extends Exception
{
    private const MESSAGE = 'No changes have been passed';

    /**
     * @return static
     * @throws BudgetRequestNoChangesPassedException
     */
    public static function throwException(): self
    {
        throw new self(self::MESSAGE, JsonResponse::HTTP_BAD_REQUEST);
    }
}