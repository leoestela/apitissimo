<?php


namespace App\Tests\Functional\Api\Action\Category;

use App\Api\EndpointUri;
use App\Tests\Functional\Api\Action\ActionWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListAllTest extends ActionWebTestCase
{
    private const ACTION = 'GET';


    public function setUp()
    {
        parent::setUp();
    }

    public function testListAllGetsAllCategoriesWhenCategoryTableIsEmpty()
    {
        $response = $this->doRequest(self::ACTION, EndpointUri::URI_CATEGORY_LIST);

        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEmpty($responseData);
    }

    public function testListAllGetsAllCategoriesWhenCategoriesLoaded()
    {
        $this->loadFixtures();

        $response = $this->doRequest(self::ACTION, EndpointUri::URI_CATEGORY_LIST);

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