<?php


namespace App\Tests\Functional\Api\Action\Category;

use App\Api\EndpointUri;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListAllTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testListAllGetsAllCategoriesWhenCategoryTableIsEmpty()
    {
        $response = $this->sendRequest('GET', EndpointUri::URI_CATEGORY_LIST);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseData);
    }

    public function testListAllGetsAllCategoriesWhenCategoriesLoaded()
    {
        $this->loadFixtures();

        $response = $this->sendRequest('GET', EndpointUri::URI_CATEGORY_LIST);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseData);
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}