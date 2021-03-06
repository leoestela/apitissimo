<?php


namespace App\Api;


class EndpointUri
{
    public const URI_CATEGORY_LIST = 'categorias';
    public const URI_BUDGET_REQUEST = 'solicitudes-presupuesto';
    public const URI_BUDGET_REQUEST_MODIFY = self::URI_BUDGET_REQUEST . '/{budgetRequestId}/modificar';
    public const URI_BUDGET_REQUEST_PUBLISH = self::URI_BUDGET_REQUEST . '/{budgetRequestId}/publicar';
    public const URI_BUDGET_REQUEST_DISCARD = self::URI_BUDGET_REQUEST . '/{budgetRequestId}/descartar';

    public static function getUriForBudgetRequestModify($budgetRequestId): string
    {
        return str_replace('{budgetRequestId}', $budgetRequestId, self::URI_BUDGET_REQUEST_MODIFY);
    }

    public static function getUriForBudgetRequestPublish($budgetRequestId): string
    {
        return str_replace('{budgetRequestId}', $budgetRequestId, self::URI_BUDGET_REQUEST_PUBLISH);
    }

    public static function getUriForBudgetRequestDiscard($budgetRequestId): string
    {
        return str_replace('{budgetRequestId}', $budgetRequestId, self::URI_BUDGET_REQUEST_DISCARD);
    }
}