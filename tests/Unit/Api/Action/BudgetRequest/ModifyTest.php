<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Modify;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ModifyTest extends ModifyBudgetRequestTestCase
{
    /** @var Modify */
    private $action;


    public function setUp()
    {
        parent::setUp();

        $this->action = new Modify($this->budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNull()
    {
        $payload = [];

        $this->doRequest(
            $this->action,
            DataFixtures::BUDGET_REQUEST_ID,
            json_encode($payload),
            JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNotValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID
        ];

        $invalidPayload = substr(json_encode($payload), 2);

        $request = new Request([], [], [], [], [], [], $invalidPayload);

        $response = $this->action->__invoke(DataFixtures::BUDGET_REQUEST_ID, $request);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID
        ];

        $this->budgetRequestServiceProphecy
            ->getBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->doRequest(
            $this->action,
            DataFixtures::BUDGET_REQUEST_INVALID_ID,
            json_encode($payload),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    public function testShouldThrowNotAllowedIfActualStatusDifferentToPending()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID
        ];

        $budgetRequest = $this->createFakeBudgetRequest(DataFixtures::BUDGET_REQUEST_TITLE, null);
        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest(
            $this->action,
            DataFixtures::BUDGET_REQUEST_ID,
            json_encode($payload),
            JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testModifyBudgetRequestIfPayloadIsValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION
        ];

        $budgetRequest = $this->createFakeBudgetRequest(DataFixtures::BUDGET_REQUEST_TITLE, null);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        try
        {
            $this->budgetRequestServiceProphecy->modifyBudgetRequest(
                $budgetRequest,
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                null,
                Status::STATUS_PENDING
            )->shouldBeCalledOnce();
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_ID, json_encode($payload), JsonResponse::HTTP_OK);
    }
}