<?php


namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class UserService
{
    /** @var ManagerRegistry */
    private $managerRegistry;

    /** @var UserRepository */
    private $userRepository;


    public function __construct(ManagerRegistry $managerRegistry, UserRepository $userRepository)
    {
        $this->managerRegistry = $managerRegistry;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $email
     * @param string $phone
     * @param string $address
     * @return User
     * @throws Exception
     */
    public function actualizeUser(string $email, string $phone, string $address): User
    {
        $this->requiredFieldInformed($email);
        $this->requiredFieldInformed($phone);
        $this->requiredFieldInformed($address);

        if (!$this->isValidEmail($email))
        {
            throw new Exception('Invalid e-mail', 100);
        }

        $user = $this->userRepository->findOneByEmail($email);

        if (null == $user)
        {
            $user = $this->createUser($email, $phone, $address);
        }
        else 
        {
            $user = $this->modifyUser($user, $email, $phone, $address);
        }

        return $user;
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

    private function allRequiredFieldsArePresent(string $email, string $phone, string $address):bool
    {
        $allRequiredFieldArePresent = true;

        if (null == $email || null == $phone || null == $address)
        {
            $allRequiredFieldArePresent = false;
        }

        return $allRequiredFieldArePresent;
    }

    private function isValidEmail(string $email):bool
    {
        return (false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    private function saveUser(User $user)
    {
        $entityManager = $this->managerRegistry->getManagerForClass('App\Entity\User');
        $entityManager->persist($user);
        $entityManager->flush();
    }

    private function createUser(string $email, string $phone, string $address): User
    {
        $user = new User($email, $phone, $address);

        $this->saveUser($user);

        return $user;
    }

    private function modifyUser(User $user, string $email, string $phone, string $address): User
    {
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setAddress($address);

        $this->saveUser($user);

        return $user;
    }
}