<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Task[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method Task[] findAll()
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[]
     */
    public function findLastLogged(int $limit): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->orderBy('t.lastLoggedAt', 'desc');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}