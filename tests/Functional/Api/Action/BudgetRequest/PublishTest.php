<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\Api\Action\ActionWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublishTest extends ActionWebTestCase
{
    private const ACTION = 'PUT';


    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsNotNumeric()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::BUDGET_REQUEST_NON_NUMERIC_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsNegative()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::BUDGET_REQUEST_NEGATIVE_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsFloat()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::BUDGET_REQUEST_FLOAT_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestNotExists()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::BUDGET_REQUEST_INVALID_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowNotAllowedExceptionIsBudgetRequestIsNotPending()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::DISCARDED_BUDGET_REQUEST_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    public function testShouldThrowNotAllowedExceptionIfBudgetRequestIfTitleIsNull()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::BUDGET_REQUEST_WITHOUT_TITLE_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    public function testShouldThrowNotAllowedExceptionIfBudgetRequestIfCategoryIsNull()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::BUDGET_REQUEST_WITHOUT_CATEGORY_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    public function testShouldPublishBudgetRequestIfRequestIsValid()
    {
        $response = $this->doRequest(
            self::ACTION, 
            EndpointUri::getUriForBudgetRequestPublish(DataFixtures::BUDGET_REQUEST_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}