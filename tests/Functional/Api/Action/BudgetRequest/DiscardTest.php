<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\Api\Action\ActionWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class DiscardTest extends ActionWebTestCase
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
            EndpointUri::getUriForBudgetRequestDiscard(DataFixtures::BUDGET_REQUEST_NON_NUMERIC_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsNegative()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestDiscard(DataFixtures::BUDGET_REQUEST_NEGATIVE_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsFloat()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestDiscard(DataFixtures::BUDGET_REQUEST_FLOAT_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExists()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestDiscard(DataFixtures::BUDGET_REQUEST_INVALID_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowNotAllowedExceptionIfBudgetRequestIsDiscarded()
    {
        $response = $this->doRequest(
            self::ACTION,
            EndpointUri::getUriForBudgetRequestDiscard(DataFixtures::DISCARDED_BUDGET_REQUEST_ID)
        );

        $this->assertEquals(JsonResponse::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    public function testShouldDiscardBudgetRequestIfRequestIsValid()
    {
        $response =
            $this->doRequest(self::ACTION, EndpointUri::getUriForBudgetRequestDiscard(DataFixtures::BUDGET_REQUEST_ID));

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}