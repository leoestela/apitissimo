<?php


namespace App\Tests\Functional\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\DataFixtures\CategoryFixtures;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class CreateTest extends FunctionalWebTestCase
{
    private const BUDGET_REQUEST_TITLE = 'Título solicitud 1';
    private const BUDGET_REQUEST_DESCRIPTION = 'Descripción solicitud 1';
    private const CATEGORY_ID = 1;
    private const USER_EMAIL = 'leoestela@hotmail.com';
    private const USER_PHONE = '971473858';
    private const USER_ADDRESS = 'Batle Biel Bibiloni 2 2B';


    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(new CategoryFixtures());
    }

    public function testCreatesNewBudgetRequest()
    {
        $payload = [
            'title' => self::BUDGET_REQUEST_TITLE,
            'description' => self::BUDGET_REQUEST_DESCRIPTION,
            'categoryId' => self::CATEGORY_ID,
            'user_data' => [
                'email' => self::USER_EMAIL,
                'phone' => self::USER_PHONE,
                'address' => self::USER_ADDRESS
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

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}