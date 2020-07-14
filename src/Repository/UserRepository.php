<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string
     * @return null|object
     */
    public function findOneByEmail($email): ?User
    {
        $entityManager = $this->getEntityManager();

        $userRepository = $entityManager->getRepository(User::class);

        return $userRepository->findOneBy(['email' => $email]);
    }
}