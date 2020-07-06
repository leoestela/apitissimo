<?php


namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class UserService extends ValidationService
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
     * @param int $phone
     * @param string $address
     * @return User
     * @throws Exception
     */
    public function actualizeUser(string $email, int $phone, string $address): User
    {
        $this->userValidData($email, $phone, $address);

        $user = $this->userRepository->findOneByEmail($email);

        if  (null != $user && false == $this->sameUserData($user, $phone, $address))
        {
            $user = $this->modifyUser($user, $phone, $address);
        }

        if (null == $user)
        {
            $user = $this->createUser($email, $phone, $address);
        }

        return $user;
    }

    private function createUser(string $email, int $phone, string $address): User
    {
        $user = new User($email, $phone, $address);

        $this->saveUser($user);

        return $user;
    }

    private function sameUserData(User $user, int $phone, string $address):bool
    {
        return $user->getPhone() == $phone && $user->getAddress() == $address;
    }

    private function modifyUser(User $user, int $phone, string $address): User
    {
        $user->setPhone($phone);
        $user->setAddress($address);

        $this->saveUser($user);

        return $user;
    }

    private function saveUser(User $user)
    {
        $entityManager = $this->managerRegistry->getManagerForClass('App\Entity\User');
        $entityManager->persist($user);
        $entityManager->flush();
    }
}