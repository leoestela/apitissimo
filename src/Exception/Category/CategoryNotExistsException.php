<?php


namespace App\Exception\Category;


use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryNotExistsException extends Exception
{
    private const MESSAGE = 'Category with ID %s not exists';

    /**
     * @param $id
     * @return static
     * @throws CategoryNotExistsException
     */
    public static function withCategoryId($id): self
    {
        throw new self(\sprintf(self::MESSAGE, $id), JsonResponse::HTTP_BAD_REQUEST);
    }
}