<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\ListPaginated;
use App\Api\Serializer;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\User;
use App\Repository\BudgetRequestRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class ListPaginatedTest extends TestCase
{
    /** @var ListPaginated */
    private $action;

    /** @var ObjectProphecy|UserService */
    private $userServiceProphecy;

    /** @var ObjectProphecy|BudgetRequestRepository */
    private $budgetRequestRepositoryProphecy;

    /** @var ObjectProphecy|User */
    private $userProphecy;

    /** @var ObjectProphecy|Serializer */
    private $serializerProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->userServiceProphecy = $this->prophesize(UserService::class);
        $userService = $this->userServiceProphecy->reveal();

        $this->budgetRequestRepositoryProphecy = $this->prophesize(BudgetRequestRepository::class);
        $budgetRequestRepository = $this->budgetRequestRepositoryProphecy->reveal();

        $this->serializerProphecy = $this->prophesize(Serializer::class);
        $serializer = $this->serializerProphecy->reveal();

        $this->userProphecy = $this->prophesize(User::class);

        $this->action = new ListPaginated($userService, $budgetRequestRepository, $serializer);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNotValid()
    {
        $payload = [
            'email' => DataFixtures::USER_EMAIL
        ];

        $invalidPayload = substr(json_encode($payload),2);

        $request = new Request([], [], [], [], [], [], $invalidPayload);

        $response = $this->action->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserEmailNotExists()
    {
        $payload = ['email' => DataFixtures::USER_EMAIL];

        $this->userServiceProphecy->getUserByEmail(DataFixtures::USER_EMAIL)->shouldBeCalledOnce()->willReturn(null);

        $this->doRequest($payload, 400);
    }

    public function testShouldReturnAllUserBudgetRequestsIfUserEmailExists()
    {
        $payload = ['email' => DataFixtures::USER_EMAIL];

        $this->userServiceProphecy
            ->getUserByEmail(DataFixtures::USER_EMAIL)->shouldBeCalledOnce()->willReturn($this->userProphecy);

        $this->budgetRequestRepositoryProphecy
            ->findByWithPagination(
            Argument::that(
            function(array $criteria):bool
            {
                return true;
            }), null, null, null)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->userProphecy->getId()->shouldBeCalledOnce()->willReturn(DataFixtures::USER_ID);

        $this->doRequest($payload, 200);
    }

    private function doRequest(array $payload, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->action->__invoke($request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }
}