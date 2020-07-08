<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Modify;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\User;
use App\Service\BudgetRequestService;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class ModifyTest extends TestCase
{
    /** @var Modify */
    private $action;

    /**
     * @var ObjectProphecy|BudgetRequestService
     */
    private $budgetRequestServiceProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestServiceProphecy = $this->prophesize(BudgetRequestService::class);
        $budgetRequestService = $this->budgetRequestServiceProphecy->reveal();

        $this->action = new Modify($budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNull()
    {
        $payload = [];

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, $payload, 400);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNotValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID
        ];

        $invalidPayload = substr(json_encode($payload),2);

        $request = new Request([], [], [], [], [], [], $invalidPayload);

        $response = $this->action->__invoke(DataFixtures::BUDGET_REQUEST_ID, $request);

        $this->assertEquals(400, $response->getStatusCode());
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

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID, $payload, 400);
    }

    public function testShouldThrowBadRequestExceptionIfReceivesStatusDifferentToPending()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'status' => Status::STATUS_PUBLISHED
        ];

        $this->mockFindBudgetRequest();

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, $payload, 400);
    }

    public function testShouldThrowBadRequestExceptionIfActualStatusDifferentToPending()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID
        ];

        $this->mockFindBudgetRequest(Status::STATUS_PUBLISHED);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, $payload, 400);
    }

    public function testModifyBudgetRequestIfPayloadIsValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION
        ];

        $budgetRequest = $this->mockFindBudgetRequest();

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

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, $payload, 201);
    }

    private function mockFindBudgetRequest(string $status = ''): BudgetRequest
    {
        $user = new User(DataFixtures::USER_EMAIL, DataFixtures::USER_EMAIL, DataFixtures::USER_ADDRESS);

        $budgetRequest = new BudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            $user
        );

        if(null != $status)
        {
            $budgetRequest->setStatus($status);
        }

        $this->budgetRequestServiceProphecy
            ->getBudgetRequestById(DataFixtures::BUDGET_REQUEST_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        return $budgetRequest;
    }

    private function doRequest(int $budgetRequestId, array $payload, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->action->__invoke($budgetRequestId, $request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }
}