<?php


namespace App\Tests\Functional\Service;


use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Repository\BudgetRequestRepository;
use App\Service\BudgetRequestService;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class BudgetRequestServiceTest extends FunctionalWebTestCase
{
    /** @var BudgetRequest */
    private $budgetRequest;

    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var BudgetRequestRepository */
    private $budgetRequestRepository;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestService = static::$container->get('budget_request_service');

        $this->budgetRequestRepository = static::$container->get(BudgetRequestRepository::class);
    }

    public function testCreateBudgetRequestWithInvalidCategory()
    {
        try
        {
            $this->budgetRequest = $this->budgetRequestService->createBudgetRequest(
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                DataFixtures::CATEGORY_ID,
                DataFixtures::USER_EMAIL,
                DataFixtures::USER_PHONE,
                DataFixtures::USER_ADDRESS
            );
        }
        catch (Exception $exception)
        {
            $this->assertEquals(400, $exception->getCode());
        }
    }

    public function testCreateBudgetRequestWithoutCategory()
    {
        try
        {
            $this->budgetRequest = $this->budgetRequestService->createBudgetRequest(
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                null,
                DataFixtures::USER_EMAIL,
                DataFixtures::USER_PHONE,
                DataFixtures::USER_ADDRESS
            );
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->budgetRequestCreatedAndDataPassedAreEquals();
    }

    public function testCreateBudgetRequestWithValidCategory()
    {
        $this->loadFixtures();

        try
        {
            $this->budgetRequest = $this->budgetRequestService->createBudgetRequest(
                DataFixtures::BUDGET_REQUEST_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                DataFixtures::CATEGORY_ID,
                DataFixtures::USER_EMAIL,
                DataFixtures::USER_PHONE,
                DataFixtures::USER_ADDRESS
            );
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->budgetRequestCreatedAndDataPassedAreEquals();
        $this->assertEquals(DataFixtures::CATEGORY_ID, $this->budgetRequest->getCategory()->getId());
        $this->assertNotEmpty($this->budgetRequest->getId());
    }

    public function testModifyBudgetRequestWithoutCategory()
    {
        $this->loadFixtures();

        $budgetRequest = $this->budgetRequestRepository->findBudgetRequestById(1);

        try
        {
            $this->budgetRequest = $this->budgetRequestService->modifyBudgetRequest(
                $budgetRequest,
                DataFixtures::BUDGET_REQUEST_NEW_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                null
            );
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->budgetRequestModifiedFieldsAndDataPassedAreEquals();
    }

    public function testModifyBudgetRequestWithCategory()
    {
        $this->loadFixtures();

        $budgetRequest = $this->budgetRequestRepository->findBudgetRequestById(1);

        try
        {
            $this->budgetRequest = $this->budgetRequestService->modifyBudgetRequest(
                $budgetRequest,
                DataFixtures::BUDGET_REQUEST_NEW_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                DataFixtures::CATEGORY_ID
            );
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $this->budgetRequestModifiedFieldsAndDataPassedAreEquals();
    }

    private function budgetRequestCreatedAndDataPassedAreEquals()
    {
        $this->assertEquals(DataFixtures::BUDGET_REQUEST_TITLE, $this->budgetRequest->getTitle());
        $this->assertEquals(DataFixtures::BUDGET_REQUEST_DESCRIPTION, $this->budgetRequest->getDescription());
        $this->assertEquals(Status::STATUS_PENDING, $this->budgetRequest->getStatus());
        $this->assertEquals(DataFixtures::USER_EMAIL, $this->budgetRequest->getUser()->getEmail());
        $this->assertEquals(DataFixtures::USER_PHONE, $this->budgetRequest->getUser()->getPhone());
        $this->assertEquals(DataFixtures::USER_ADDRESS, $this->budgetRequest->getUser()->getAddress());
    }

    private function budgetRequestModifiedFieldsAndDataPassedAreEquals()
    {
        $this->assertEquals(DataFixtures::BUDGET_REQUEST_NEW_TITLE, $this->budgetRequest->getTitle());
        $this->assertEquals(DataFixtures::BUDGET_REQUEST_DESCRIPTION, $this->budgetRequest->getDescription());
    }

    /**
     * @throws Exception
     */
    public function tearDown()
    {
        $this->purge();
    }
}