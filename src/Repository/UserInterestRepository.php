<?php

namespace App\Repository;

use App\Entity\UserInterest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterest::class);
    }

    public function findPopularCourses(int $limit = 5)
    {
        return $this->createQueryBuilder('ui')
            ->select('c.id, c.title, COUNT(ui.id) as interestCount')
            ->join('ui.course', 'c')
            ->groupBy('c.id')
            ->orderBy('interestCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
} 