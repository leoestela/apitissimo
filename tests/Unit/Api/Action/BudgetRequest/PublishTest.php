<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Publish;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\BudgetRequestRepository;
use App\Service\BudgetRequestService;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class PublishTest extends TestCase
{
    /** @var Publish */
    private $action;

    /** @var ObjectProphecy|BudgetRequestRepository */
    private $budgetRequestRepositoryProphecy;

    /** @var ObjectProphecy|BudgetRequestService */
    private $budgetRequestServiceProphecy;

    /** @var ObjectProphecy|BudgetRequest */
    private $budgetRequestProphecy;

    /** @var ObjectProphecy|Category */
    private $categoryProphecy;

    /** @var Category */
    private $category;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestRepositoryProphecy = $this->prophesize(BudgetRequestRepository::class);
        $budgetRequestRepository = $this->budgetRequestRepositoryProphecy->reveal();

        $this->budgetRequestServiceProphecy = $this->prophesize(BudgetRequestService::class);
        $budgetRequestService = $this->budgetRequestServiceProphecy->reveal();

        $this->budgetRequestProphecy = $this->prophesize(BudgetRequest::class);

        $this->categoryProphecy = $this->prophesize(Category::class);
        $this->category = $this->categoryProphecy->reveal();

        $this->action = new Publish($budgetRequestRepository, $budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,400);
    }

    public function testShouldThrowBadRequestExceptionIfActualStatusIsNotPending()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::CATEGORY_NAME
        );

        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,400);
    }

    public function testShouldThrowBadRequestExceptionIfActualTitleIsNull()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(null, DataFixtures::CATEGORY_NAME);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,400);
    }

    public function testShouldThrowBadRequestExceptionIfActualCategoryIsNull()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            null
        );

        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_INVALID_ID)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID,400);
    }

    public function testShouldPublishBudgetRequestIfActualStatusIsPending()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::CATEGORY_NAME
        );

        $budgetRequest->setStatus(Status::STATUS_PENDING);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(DataFixtures::BUDGET_REQUEST_ID)
            ->shouldBeCalledOnce()
            ->willReturn($this->budgetRequestProphecy);

        $this->budgetRequestProphecy->getTitle()->shouldBeCalled()->willReturn(DataFixtures::BUDGET_REQUEST_TITLE);
        $this->budgetRequestProphecy
            ->getDescription()->shouldBeCalled()->willReturn(DataFixtures::BUDGET_REQUEST_DESCRIPTION);
        $this->budgetRequestProphecy->getCategory()->shouldBeCalled()->willReturn($this->categoryProphecy);
        $this->budgetRequestProphecy->getStatus()->shouldBeCalledOnce()->willReturn(Status::STATUS_PENDING);

        $this->categoryProphecy->getId()->shouldBeCalled()->willReturn(DataFixtures::CATEGORY_ID);

        try
        {
            $this->budgetRequestServiceProphecy->modifyBudgetRequest(
                $this->budgetRequestProphecy,
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                DataFixtures::CATEGORY_ID,
                Status::STATUS_PUBLISHED
            )->shouldBeCalledOnce();
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID,201);
    }

    private function doRequest(int $budgetRequestId, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], []);

        $response = $this->action->__invoke($budgetRequestId, $request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    private function mockCreateBudgetRequest(?string $budgetRequestTitle, ?string $categoryName): BudgetRequest
    {
        $user = new User(
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_ADDRESS
        );

        $category = null;

        if(null != $categoryName)
        {
            $category = new Category(DataFixtures::CATEGORY_NAME, null);
        }

        return new BudgetRequest(
            $budgetRequestTitle,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            $category,
            $user
        );
    }
}