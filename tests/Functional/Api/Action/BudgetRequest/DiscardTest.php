<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class DiscardTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testDiscardBudgetRequest()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            str_replace('{budgetRequestId}', '1',EndpointUri::URI_BUDGET_REQUEST_DISCARD),
            [],
            [],
            [],
            null,
            true);

        echo($client->getResponse()->getContent());
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}