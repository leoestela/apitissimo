<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListPaginatedTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testListAllGetsAllBudgetRequestsWhenBudgetRequestTableIsEmpty()
    {
        $response = $this->sendRequest('GET', EndpointUri::URI_BUDGET_REQUEST);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseData);
    }

    public function testListAllGetsAllBudgetRequestsWhenBudgetRequestsLoaded()
    {
        $this->loadFixtures();

        $response = $this->sendRequest('GET', EndpointUri::URI_BUDGET_REQUEST);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($responseData);
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}