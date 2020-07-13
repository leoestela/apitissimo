<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\Api\Action\ActionWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublishTest extends ActionWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsNotNumeric()
    {
        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_NON_NUMERIC_ID));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestNotExists()
    {
        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_INVALID_ID));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestIsNotPending()
    {
        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::DISCARDED_BUDGET_REQUEST_ID));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestIfTitleIsNull()
    {
        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_WITHOUT_TITLE_ID));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestIfCategoryIsNull()
    {
        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_WITHOUT_CATEGORY_ID));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldPublishBudgetRequestIfRequestIsValid()
    {
        $response = $this->doRequest('PUT', $this->getUri(DataFixtures::BUDGET_REQUEST_ID));

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }

    private function getUri($budgetRequestId): string
    {
        return str_replace('{budgetRequestId}', $budgetRequestId, EndpointUri::URI_BUDGET_REQUEST_PUBLISH);
    }
}