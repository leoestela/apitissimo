<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Discard;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\User;
use App\Repository\BudgetRequestRepository;
use App\Service\BudgetRequestService;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class DiscardTest extends TestCase
{
    /** @var Discard */
    private $action;

    /** @var ObjectProphecy|BudgetRequestRepository */
    private $budgetRequestRepositoryProphecy;

    /** @var ObjectProphecy|BudgetRequestService */
    private $budgetRequestServiceProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestRepositoryProphecy = $this->prophesize(BudgetRequestRepository::class);
        $budgetRequestRepository = $this->budgetRequestRepositoryProphecy->reveal();

        $this->budgetRequestServiceProphecy = $this->prophesize(BudgetRequestService::class);
        $budgetRequestService = $this->budgetRequestServiceProphecy->reveal();

        $this->action = new Discard($budgetRequestRepository, $budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,400);
    }

    public function testShouldThrowBadRequestExceptionIfActualStatusIsDiscarded()
    {
        $budgetRequest = $this->createFakeBudgetRequest();

        $budgetRequest->setStatus(Status::STATUS_DISCARDED);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID,400);
    }

    public function testShouldPublishBudgetRequestIfActualStatusIsNotDiscarded()
    {
        $budgetRequest = $this->createFakeBudgetRequest();

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        try
        {
            $this->budgetRequestServiceProphecy->modifyBudgetRequest(
                $budgetRequest,
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                null,
                Status::STATUS_DISCARDED
            )->shouldBeCalledOnce();
        }
        catch (\Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID,201);
    }

    private function doRequest(int $budgetRequestId, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], []);

        $response = $this->action->__invoke($budgetRequestId, $request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    private function createFakeBudgetRequest(): BudgetRequest
    {
        $user = new User(
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_ADDRESS
        );

        return new BudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            $user
        );
    }
}