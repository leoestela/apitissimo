<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\Api\Action\ActionWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateTest extends ActionWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionIsMissing()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionIsNull()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => null,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionIsEmptyString()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => '',
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionIsTooLong()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_TOO_LONG_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfCategoryIdIsNotNumeric()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_NON_NUMERIC_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserDataIsMissing()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserDataIsEmpty()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => []
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserDataIsNull()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => null
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserDataIsEmptyString()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => ''
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserEmailIsMissing()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserEmailIsNull()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => null,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserEmailIsEmptyString()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => '',
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserPhoneIsMissing()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserPhoneIsNull()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => null,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserPhoneIsEmptyString()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => '',
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserPhoneIsNotNumeric()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_NON_NUMERIC_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserAddressIsMissing()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserAddressIsNull()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => null
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserAddressIsEmptyString()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => DataFixtures::CATEGORY_ID,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => ''
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldCreateNewBudgetRequestIfPayloadWithoutCategoryIdIsValid()
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

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    public function testShouldCreateNewBudgetRequestIfPayloadWithNullCategoryIdIsValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => null,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    public function testShouldCreateNewBudgetRequestIfPayloadWithEmptyStringCategoryIdIsValid()
    {
        $payload = [
            'title' => DataFixtures::BUDGET_REQUEST_TITLE,
            'description' => DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => null,
            'user_data' => [
                'email' => DataFixtures::USER_EMAIL,
                'phone' => DataFixtures::USER_PHONE,
                'address' => DataFixtures::USER_ADDRESS
            ]
        ];

        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    public function testShouldCreateNewBudgetRequestIfPayloadWithCategoryIdIsValid()
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
        
        $response = $this->doRequest('POST', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}