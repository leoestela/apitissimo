<?php


namespace App\Exception\BudgetRequest;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class BudgetRequestActionNotAllowedException extends Exception
{
    private const MESSAGE = '%s is not allowed';

    /**
     * @param $action
     * @return static
     * @throws BudgetRequestActionNotAllowedException
     */
    public static function withAction($action): self
    {
        throw new self(\sprintf(self::MESSAGE, $action), JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }
}