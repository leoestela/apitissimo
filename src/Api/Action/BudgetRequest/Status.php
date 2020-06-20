<?php


namespace App\Api\Action\BudgetRequest;


abstract class Status
{
    public const STATUS_PENDING = 'Pendiente';
    public const STATUS_PUBLISHED = 'Publicada';
    public const STATUS_DISCARDED = 'Descartada';
}