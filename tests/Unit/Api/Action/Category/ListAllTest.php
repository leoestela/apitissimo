<?php


namespace App\Tests\Unit\Api\Action\Category;


use App\Api\Action\Category\ListAll;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class ListAllTest extends TestCase
{
    /** @var ObjectProphecy|CategoryRepository */
    private $categoryRepositoryProphecy;

    /** @var ListAll */
    private $action;

    public function setUp()
    {
        parent::setUp();

        $this->categoryRepositoryProphecy = $this->prophesize(CategoryRepository::class);
        $categoryRepository = $this->categoryRepositoryProphecy->reveal();

        $this->action = new ListAll($categoryRepository);
    }

    public function testShouldGetAllCategories():void
    {
        $request = new Request([], [], [], [], [], [], []);

        $response = $this->action->__invoke($request);

        $this->categoryRepositoryProphecy->findAll()->shouldBeCalledOnce()->willReturn(null);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }
}