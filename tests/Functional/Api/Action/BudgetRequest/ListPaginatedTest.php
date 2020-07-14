<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\Api\Action\ActionWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListPaginatedTest extends ActionWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testShouldThrowBadRequestExceptionIfEmailIsNotValid()
    {
        $this->loadFixtures();

        $payload = ['email' => DataFixtures::USER_INVALID_EMAIL];

        $response = $this->doRequest('GET', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testShouldListAllGetsAllBudgetRequestsWhenBudgetRequestTableIsEmpty()
    {
        $response = $this->doRequest('GET', EndpointUri::URI_BUDGET_REQUEST);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseData);
    }

    public function testShouldListAllGetsAllBudgetRequestsWhenBudgetRequestsLoaded()
    {
        $this->loadFixtures();

        $response = $this->doRequest('GET', EndpointUri::URI_BUDGET_REQUEST);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseData);
    }

    public function testShouldListAllGetsAllBudgetRequestsForEmailPassedWhenBudgetRequestsLoaded()
    {
        $this->loadFixtures();

        $payload = ['email' => DataFixtures::USER_EMAIL];

        $response = $this->doRequest('GET', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseData);
    }

    public function testShouldListAllGetsAllBudgetRequestsForEmailAndPaginationParamsPassedWhenBudgetRequestsLoaded()
    {
        $this->loadFixtures();

        $payload = ['email' => DataFixtures::USER_EMAIL, 'limit' => 2, 'offset' => 2];

        $response = $this->doRequest('GET', EndpointUri::URI_BUDGET_REQUEST, $payload);

        $responseData = json_decode($response->getContent(), true);

        $budgetRequestCount = count($responseData['budget_requests']);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(2, $budgetRequestCount);
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}