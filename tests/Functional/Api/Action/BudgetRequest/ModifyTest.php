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

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_NON_NUMERIC_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsNegative()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_NEGATIVE_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowExceptionIfBudgetRequestIdIsFloat()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_FLOAT_ID];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_NON_NUMERIC_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfPayloadIsNull()
    {
        $payload = [];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExists()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_INVALID_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIsBudgetRequestIsNotPending()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::DISCARDED_BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfTitlePassedIsTooLong()
    {
        $payload = ['title' => DataFixtures::TOO_LONG_TEXT];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionPassedIsNull()
    {
        $payload = ['description' => null];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionPassedIsEmptyString()
    {
        $payload = ['description' => ''];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionPassedIsTooLong()
    {
        $payload = ['description' => DataFixtures::TOO_LONG_TEXT];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfCategoryIdPassedIsNotNumeric()
    {
        $payload = ['category_id' => DataFixtures::CATEGORY_NON_NUMERIC_ID];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfCategoryIdPassedIsNegative()
    {
        $payload = ['category_id' => DataFixtures::CATEGORY_NEGATIVE_ID];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfCategoryIdPassedIsFloat()
    {
        $payload = ['category_id' => DataFixtures::CATEGORY_FLOAT_ID];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfSameInfoPassed()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_TITLE];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::DISCARDED_BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldModifyBudgetRequestIfBudgetRequestExistsAndPayloadIsValid()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $response = $this->doRequest(
            'PUT',
            EndpointUri::getUriForBudgetRequestModify(DataFixtures::BUDGET_REQUEST_ID),
            $payload
        );

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}