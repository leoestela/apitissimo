<?php


namespace App\Repository;


use App\Entity\BudgetRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class BudgetRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetRequest::class);
    }

    public function findBudgetRequestById(int $budgetRequestId)
    {
        return parent::findOneBy(['id' => $budgetRequestId], null);
    }

    public function findByWithPagination(array $criteria, ?array $orderBy, ?int $limit, ?int $offset)
    {
        return parent::findBy([], null, $limit, $offset);
    }
}