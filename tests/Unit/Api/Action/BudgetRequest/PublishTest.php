<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Publish;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Entity\User;
use App\Service\BudgetRequestService;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PublishTest extends TestCase
{
    /** @var Publish */
    private $action;

    /** @var ObjectProphecy|BudgetRequestService */
    private $budgetRequestServiceProphecy;

    /** @var ObjectProphecy|BudgetRequest */
    private $budgetRequestProphecy;

    /** @var ObjectProphecy|Category */
    private $categoryProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestServiceProphecy = $this->prophesize(BudgetRequestService::class);
        $budgetRequestService = $this->budgetRequestServiceProphecy->reveal();

        $this->budgetRequestProphecy = $this->prophesize(BudgetRequest::class);

        $this->categoryProphecy = $this->prophesize(Category::class);

        $this->action = new Publish($budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID, null);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowBadRequestExceptionIfActualStatusIsNotPending()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::CATEGORY_NAME
        );

        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowBadRequestExceptionIfActualTitleIsNull()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(null, DataFixtures::CATEGORY_NAME);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowBadRequestExceptionIfActualCategoryIsNull()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            null
        );

        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldPublishBudgetRequestIfActualStatusIsPending()
    {
        $budgetRequest = $this->mockCreateBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::CATEGORY_NAME
        );

        $budgetRequest->setStatus(Status::STATUS_PENDING);

        $this->budgetRequestServiceProphecy
            ->getBudgetRequestById(DataFixtures::BUDGET_REQUEST_ID)
            ->shouldBeCalledOnce()
            ->willReturn($this->budgetRequestProphecy);

        $this->budgetRequestProphecy->getTitle()->shouldBeCalled()->willReturn(DataFixtures::BUDGET_REQUEST_TITLE);
        $this->budgetRequestProphecy
            ->getDescription()
            ->shouldBeCalled()
            ->willReturn(DataFixtures::BUDGET_REQUEST_DESCRIPTION
            );
        $this->budgetRequestProphecy->getCategory()->shouldBeCalled()->willReturn($this->categoryProphecy);
        $this->budgetRequestProphecy->getStatus()->shouldBeCalledOnce()->willReturn(Status::STATUS_PENDING);

        $this->categoryProphecy->getId()->willReturn(DataFixtures::CATEGORY_ID);

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

        $this->doRequest(DataFixtures::BUDGET_REQUEST_ID, JsonResponse::HTTP_OK);
    }

    private function mockGetBudgetRequest(int $budgetRequestId, ?BudgetRequest $budgetRequest)
    {
        $this->budgetRequestServiceProphecy
            ->getBudgetRequestById($budgetRequestId)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);
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

    private function doRequest(int $budgetRequestId, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], []);

        $response = $this->action->__invoke($budgetRequestId, $request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }
}