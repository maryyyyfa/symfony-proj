<?php

namespace App\Repository;

use App\Entity\CourseProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourseProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseProgress::class);
    }

    public function save(CourseProgress $courseProgress, bool $flush = false): void
    {
        $this->getEntityManager()->persist($courseProgress);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CourseProgress $courseProgress, bool $flush = false): void
    {
        $this->getEntityManager()->remove($courseProgress);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserAndCourse($user, $course)
    {
        return $this->createQueryBuilder('cp')
            ->andWhere('cp.user = :user')
            ->andWhere('cp.course = :course')
            ->setParameter('user', $user)
            ->setParameter('course', $course)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 