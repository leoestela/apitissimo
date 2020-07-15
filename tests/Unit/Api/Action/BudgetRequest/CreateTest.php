<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Create;
use App\DataFixtures\DataFixtures;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CreateTest extends ActualizeBudgetRequestTestCase
{
    /** @var Create */
    private $action;


    public function setUp()
    {
        parent::setUp();

        $this->action = new Create($this->budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNull()
    {
        $payload = [];

        $this->doRequest($payload, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNotValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $invalidPayload = substr(json_encode($payload),2);

        $request = new Request([], [], [], [], [], [], $invalidPayload);

        $response = $this->action->__invoke($request);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldCreateBudgetRequestIfPayloadIsValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        try {
            $this->budgetRequestServiceProphecy->createBudgetRequest(
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                null,
                DataFixtures::USER_EMAIL,
                DataFixtures::USER_PHONE,
                DataFixtures::USER_ADDRESS)->shouldBeCalledOnce();
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->doRequest($payload, JsonResponse::HTTP_CREATED);
    }

    private function doRequest(array $payload, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->action->__invoke($request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }
}