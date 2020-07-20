<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class ModifyBudgetRequestTestCase extends ActualizeBudgetRequestTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function createFakeBudgetRequest(?string $budgetRequestTitle, ?string $categoryName): BudgetRequest
    {
        $user = new User(
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_PHONE,
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

    protected function mockGetBudgetRequest(int $budgetRequestId, ?BudgetRequest $budgetRequest)
    {
        $this->budgetRequestServiceProphecy
            ->getBudgetRequestById($budgetRequestId)
            ->shouldBeCalledOnce()
            ->willReturn($budgetRequest);
    }

    protected function doRequest($action, int $budgetRequestId, ?string $requestContent, int $expectedStatusCode)
    {
        $request = new Request([], [], [], [], [], [], $requestContent);

        $response = $action->__invoke($budgetRequestId, $request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }
}