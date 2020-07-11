<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Discard;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\User;
use App\Service\BudgetRequestService;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DiscardTest extends TestCase
{
    /** @var Discard */
    private $action;

    /** @var ObjectProphecy|BudgetRequestService */
    private $budgetRequestServiceProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestServiceProphecy = $this->prophesize(BudgetRequestService::class);
        $budgetRequestService = $this->budgetRequestServiceProphecy->reveal();

        $this->action = new Discard($budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID, null);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowBadRequestExceptionIfActualStatusIsDiscarded()
    {
        $budgetRequest = $this->createFakeBudgetRequest();

        $budgetRequest->setStatus(Status::STATUS_DISCARDED);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldPublishBudgetRequestIfActualStatusIsNotDiscarded()
    {
        $budgetRequest = $this->createFakeBudgetRequest();

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

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
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, JsonResponse::HTTP_OK);
    }

    private function mockGetBudgetRequest(int $budgetRequestId, ?BudgetRequest $budgetRequest)
    {
        $this->budgetRequestServiceProphecy
            ->getBudgetRequestById($budgetRequestId)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);
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

    private function doRequest(int $budgetRequestId, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], []);

        $response = $this->action->__invoke($budgetRequestId, $request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }
}