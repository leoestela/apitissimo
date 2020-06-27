<?php


namespace App\Tests\Unit\Api\Action\BudgetRequest;


use App\Api\Action\BudgetRequest\Create;
use App\Entity\BudgetRequest;
use App\Service\BudgetRequestService;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class CreateTest extends TestCase
{
    private const BUDGET_REQUEST_TITLE = 'Título solicitud 1';
    private const BUDGET_REQUEST_DESCRIPTION = 'Descripción solicitud 1';
    private const CATEGORY_ID = 1;
    private const USER_EMAIL = 'leoestela@hotmail.com';
    private const USER_PHONE = '971473858';
    private const USER_ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var ObjectProphecy|BudgetRequestService */
    private $budgetRequestServiceProphecy;

    /** @var ObjectProphecy|BudgetRequest */
    private $budgetRequestProphecy;

    /** @var Create */
    private $action;


    public function setUp()
    {
        parent::setUp();

        $this->budgetRequestServiceProphecy = $this->prophesize(BudgetRequestService::class);
        $budgetRequestService = $this->budgetRequestServiceProphecy->reveal();

        $this->budgetRequestProphecy = $this->prophesize(BudgetRequest::class);

        $this->action = new Create($budgetRequestService);
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNull()
    {
        $payload = [];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->action->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfJsonDataIsNotValid()
    {
        $payload = [
            'title' => self::BUDGET_REQUEST_TITLE,
            'category_id' => self::CATEGORY_ID,
            'user_data' => [
                'email' => self::USER_EMAIL,
                'phone' => self::USER_PHONE,
                'address' => self::USER_ADDRESS
            ]
        ];

        $invalidPayload = substr(json_encode($payload),2);

        $request = new Request([], [], [], [], [], [], $invalidPayload);

        $response = $this->action->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfDescriptionIsMissing()
    {
        $payload = [
            'title' => self::BUDGET_REQUEST_TITLE,
            'category_id' => self::CATEGORY_ID,
            'user_data' => [
                'email' => self::USER_EMAIL,
                'phone' => self::USER_PHONE,
                'address' => self::USER_ADDRESS
            ]
        ];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->action->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldThrowBadRequestExceptionIfUserDataIsMissing()
    {
        $payload = [
            'title' => self::BUDGET_REQUEST_TITLE,
            'description' => self::BUDGET_REQUEST_DESCRIPTION,
            'category_id' => self::CATEGORY_ID
        ];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->action->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldCreateBudgetRequestIfPayloadIsValid()
    {
        $payload = [
            'title' => self::BUDGET_REQUEST_TITLE,
            'description' => self::BUDGET_REQUEST_DESCRIPTION,
            'categoryId' => self::CATEGORY_ID,
            'user_data' => [
                'email' => self::USER_EMAIL,
                'phone' => self::USER_PHONE,
                'address' => self::USER_ADDRESS
            ]
        ];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        try {
            $this->budgetRequestServiceProphecy->createBudgetRequest(
                self::BUDGET_REQUEST_TITLE,
                self::BUDGET_REQUEST_DESCRIPTION,
                null,
                self::USER_EMAIL, self::USER_PHONE,
                self::USER_ADDRESS)->shouldBeCalledOnce()->willReturn($this->budgetRequestProphecy);
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

        $response = $this->action->__invoke($request);

        $this->assertEquals(201, $response->getStatusCode());
    }
}