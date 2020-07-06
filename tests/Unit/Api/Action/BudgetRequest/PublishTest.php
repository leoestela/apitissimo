<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Publish;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\User;
use App\Repository\BudgetRequestRepository;
use App\Service\BudgetRequestService;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class PublishTest extends TestCase
{
    /** @var Publish */
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

        $this->action = new Publish($budgetRequestRepository, $budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,400);
    }

    public function testShouldThrowBadRequestExceptionIfActualStatusIsNotPending()
    {
        $user = new User(
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_ADDRESS
        );

        $budgetRequest = new BudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            $user
        );

        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,400);
    }

    public function testShouldPublishBudgetRequestIfActualStatusIsPending()
    {
        $user = new User(
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_ADDRESS
        );

        $budgetRequest = new BudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            $user
        );

        $budgetRequest->setStatus(Status::STATUS_PENDING);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        try
        {
            $this->budgetRequestServiceProphecy->modifyBudgetRequest(
                $budgetRequest,
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                null,
                Status::STATUS_PUBLISHED
            )->shouldBeCalledOnce();
        }
        catch (Exception $exception)
        {
            $this->fail();
        }

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,201);
    }

    private function doRequest(int $budgetRequestId, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], []);

        $response = $this->action->__invoke($budgetRequestId, $request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }
}