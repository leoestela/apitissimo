<?php


namespace App\Tests\Functional\Service;


use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\CategoryFixtures;
use App\Entity\BudgetRequest;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class BudgetRequestServiceTest extends FunctionalWebTestCase
{
    private const BUDGET_REQUEST_TITLE = 'Título test funcional.';
    private const BUDGET_REQUEST_DESCRIPTION = 'Descripción categoría test funcional.';
    private const BUDGET_REQUEST_CATEGORY_ID = 2;
    private const USER_EMAIL = 'leoestela@hotmail.com';
    private const USER_PHONE = '971100309';
    private const USER_ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var BudgetRequest */
    private $budgetRequest;


    public function setUp()
    {
        parent::setUp();
    }

    public function testCreateBudgetRequestWithoutCategory()
    {
        $this->budgetRequest = static::$container->get('budget_request_service')->createBudgetRequest(
            self::BUDGET_REQUEST_TITLE,
            self::BUDGET_REQUEST_DESCRIPTION,
            null,
            self::USER_EMAIL,
            self::USER_PHONE,
            self::USER_ADDRESS
        );

        $this->budgetRequestsAreEquals();
    }

    public function testCreateBudgetRequestWithCategory()
    {
        $this->loadFixtures(new CategoryFixtures());

        $this->budgetRequest = static::$container->get('budget_request_service')->createBudgetRequest(
            self::BUDGET_REQUEST_TITLE,
            self::BUDGET_REQUEST_DESCRIPTION,
            self::BUDGET_REQUEST_CATEGORY_ID,
            self::USER_EMAIL,
            self::USER_PHONE,
            self::USER_ADDRESS
        );

        $this->budgetRequestsAreEquals();
        $this->assertEquals(self::BUDGET_REQUEST_CATEGORY_ID, $this->budgetRequest->getCategory()->getId());
        $this->assertNotEmpty($this->budgetRequest->getId());
    }

    public function budgetRequestsAreEquals()
    {
        $this->assertEquals(self::BUDGET_REQUEST_TITLE, $this->budgetRequest->getTitle());
        $this->assertEquals(self::BUDGET_REQUEST_DESCRIPTION, $this->budgetRequest->getDescription());
        $this->assertEquals(Status::STATUS_PENDING, $this->budgetRequest->getStatus());
        $this->assertEquals(self::USER_EMAIL, $this->budgetRequest->getUser()->getEmail());
        $this->assertEquals(self::USER_PHONE, $this->budgetRequest->getUser()->getPhone());
        $this->assertEquals(self::USER_ADDRESS, $this->budgetRequest->getUser()->getAddress());
    }

    /**
     * @throws Exception
     */
    public function tearDown()
    {
        $this->purge();
    }
}