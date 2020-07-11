<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\DataFixtures;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    public function testCreatesNewBudgetRequest()
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

        $client = static::createClient();
        $client->request(
            'POST',
            EndpointUri::URI_BUDGET_REQUEST,
            [],
            [],
            [],
            json_encode($payload),
            true);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}