<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Discard;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class DiscardTest extends ModifyBudgetRequestTestCase
{
    /** @var Discard */
    private $action;


    public function setUp()
    {
        parent::setUp();

        $this->action = new Discard($this->budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID, null);

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_INVALID_ID, null, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowBadRequestExceptionIfActualStatusIsDiscarded()
    {
        $budgetRequest = $this->createFakeBudgetRequest(DataFixtures::BUDGET_REQUEST_TITLE, null);

        $budgetRequest->setStatus(Status::STATUS_DISCARDED);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_ID, null, JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testShouldPublishBudgetRequestIfActualStatusIsNotDiscarded()
    {
        $budgetRequest = $this->createFakeBudgetRequest(DataFixtures::BUDGET_REQUEST_TITLE, null);

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

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_ID, null, JsonResponse::HTTP_OK);
    }
}