<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ModifyTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testModifyBudgetRequest()
    {
        $payload = ['title' => DataFixtures::BUDGET_REQUEST_NEW_TITLE];

        $client = static::createClient();
        $client->request(
            'PUT',
            str_replace('{budgetRequestId}', '1',EndpointUri::URI_BUDGET_REQUEST_MODIFY),
            [],
            [],
            [],
            json_encode($payload),
            true);

        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}