<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Service\BudgetRequestService;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class ActualizeBudgetRequestTestCase extends TestCase
{
    /** @var ObjectProphecy|BudgetRequestService */
    protected $budgetRequestServiceProphecy;

    /** @var BudgetRequestService */
    protected $budgetRequestService;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestServiceProphecy = $this->prophesize(BudgetRequestService::class);
        $this->budgetRequestService = $this->budgetRequestServiceProphecy->reveal();
    }
}