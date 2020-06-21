<?php


namespace App\Service;

use App\Entity\BudgetRequest;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class BudgetRequestService
{

    /** @var UserService */
    private $userService;

    /** @var ManagerRegistry */
    private $managerRegistry;


    public function __construct(UserService $userService, ManagerRegistry $managerRegistry)
    {
        $this->userService = $userService;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param string|null $title
     * @param string $description
     * @param int|null $categoryId
     * @param string $email
     * @param string $phone
     * @param string $address
     * @return BudgetRequest
     * @throws Exception
     */
    public function createBudgetRequest(
        ?string $title,
        string $description,
        ?int $categoryId,
        string $email,
        string $phone,
        string $address):BudgetRequest
    {
        $this->requiredFieldInformed($description);
        $this->requiredFieldInformed($email);
        $this->requiredFieldInformed($phone);
        $this->requiredFieldInformed($address);

        $this->isValidEmail($email);

        $user = $this->userService->actualizeUser($email, $phone, $address);

        $budgetRequest = new BudgetRequest($title, $description, null, $user);

        $entityManager = $this->managerRegistry->getManagerForClass('App\Entity\BudgetRequest');
        $entityManager->persist($budgetRequest);
        $entityManager->flush();

        return $budgetRequest;
    }

    /**
     * @param string $requiredField
     * @throws Exception
     */
    private function requiredFieldInformed(string $requiredField)
    {
        if (null == $requiredField)
        {
            throw new Exception('Required field not informed', 100);
        }
    }

    /**
     * @param string $email
     * @throws Exception
     */
    private function isValidEmail(string $email)
    {
        if (false == filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new Exception('Invalid e-mail', 100);
        }
    }
}