<?php


namespace App\Api;


class EndpointUri
{
    public const URI_CATEGORY_LIST = 'categorias';
    public const URI_BUDGET_REQUEST = 'solicitudes-presupuesto';
    public const URI_BUDGET_REQUEST_MODIFY = self::URI_BUDGET_REQUEST . '/{budgetRequestId}/modify';
}