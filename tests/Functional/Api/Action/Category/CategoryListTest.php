<?php


namespace App\Tests\Functional\Api\Action\Category;

use App\DataFixtures\CategoryFixtures;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class CategoryListTest extends FunctionalWebTestCase
{
    private const ENDPOINT_URI = 'category/list';

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(new CategoryFixtures());
    }

    public function testCategoryListGetsAllCategories()
    {
        $response = $this->sendRequest('GET', self::ENDPOINT_URI);

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