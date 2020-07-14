<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Publish;
use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\Category;
use Exception;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublishTest extends ModifyBudgetRequestTestCase
{
    /** @var Publish */
    private $action;

    /** @var ObjectProphecy|BudgetRequest */
    private $budgetRequestProphecy;

    /** @var ObjectProphecy|Category */
    private $categoryProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestProphecy = $this->prophesize(BudgetRequest::class);

        $this->categoryProphecy = $this->prophesize(Category::class);

        $this->action = new Publish($this->budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfBudgetRequestNotExist()
    {
        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_INVALID_ID, null);

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_INVALID_ID, null, JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testShouldThrowNotAllowedExceptionIfActualStatusIsNotPending()
    {
        $budgetRequest = $this->createFakeBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::CATEGORY_NAME
        );

        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_ID, null, JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testShouldThrowNotAllowedExceptionIfActualTitleIsNull()
    {
        $budgetRequest = $this->createFakeBudgetRequest(null, DataFixtures::CATEGORY_NAME);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_ID, null, JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testShouldThrowNotAllowedExceptionIfActualCategoryIsNull()
    {
        $budgetRequest = $this->createFakeBudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            null
        );

        $budgetRequest->setStatus(Status::STATUS_PUBLISHED);

        $this->mockGetBudgetRequest(DataFixtures::BUDGET_REQUEST_ID, $budgetRequest);

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_ID, null, JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testShouldPublishBudgetRequestIfActualStatusIsPending()
    {
        $budgetRequest = $this->createFakeBudgetRequest(
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

        $this->doRequest($this->action, DataFixtures::BUDGET_REQUEST_ID, null, JsonResponse::HTTP_OK);
    }
}