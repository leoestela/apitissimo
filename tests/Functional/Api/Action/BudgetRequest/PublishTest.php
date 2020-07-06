<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class PublishTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testModifyBudgetRequest()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            str_replace('{budgetRequestId}', '1',EndpointUri::URI_BUDGET_REQUEST_PUBLISH),
            [],
            [],
            [],
            null,
            true);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}