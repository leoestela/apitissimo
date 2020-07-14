<?php


namespace App\Exception\BudgetRequest;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class BudgetRequestNotExistsException extends Exception
{
    private const MESSAGE = 'Budget request with ID %s not exists';

    /**
     * @param $id
     * @return static
     * @throws BudgetRequestNotExistsException
     */
    public static function withBudgetRequestId($id): self
    {
        throw new self(\sprintf(self::MESSAGE, $id), JsonResponse::HTTP_BAD_REQUEST);
    }
}

