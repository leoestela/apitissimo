<?php


namespace App\Api\Action\BudgetRequest;


abstract class Status
{
    public const STATUS_PENDING = 'PENDIENTE';
    public const STATUS_PUBLISHED = 'PUBLICADA';
    public const STATUS_DISCARDED = 'DESCARTADA';
}