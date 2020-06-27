<?php


namespace App\Tests\Functional\Api\Action\Category;

use App\Api\EndpointUri;
use App\DataFixtures\CategoryFixtures;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class ListAllTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(new CategoryFixtures());
    }

    public function testCategoryListGetsAllCategories()
    {
        $response = $this->sendRequest('GET', EndpointUri::URI_CATEGORY_LIST);

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