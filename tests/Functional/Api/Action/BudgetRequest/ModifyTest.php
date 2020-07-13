<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\Api\Action\ActionWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ModifyTest extends ActionWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsNotNumeric()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_NON_NUMERIC_ID), $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfPayloadIsNull()
    {
        $payload = [];

        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_ID), $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExists()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_INVALID_ID), $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestIsNotPending()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::DISCARDED_BUDGET_REQUEST_ID), $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestDescriptionPassedIsNull()
    {
        $payload = ['description' => ''];

        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_ID), $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestIfSameInfoPassed()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_TITLE];

        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::DISCARDED_BUDGET_REQUEST_ID), $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldModifyBudgetRequestIfBudgetRequestExistsAndPayloadIsValid()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_ID), $payload);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }

    private function getUri($budgetRequestId): string
    {
        return str_replace('{budgetRequestId}', $budgetRequestId, EndpointUri::URI_BUDGET_REQUEST_MODIFY);
    }
}