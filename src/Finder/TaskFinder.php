<?php

namespace App\Finder;

use Doctrine\DBAL\Connection;

class TaskFinder
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findLastLogged(int $limit): array
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('code', 'name')
            ->from('task', 't')
            ->orderBy('t.last_logged_at', 'desc')
            ->setMaxResults($limit)
            ->fetchAllAssociative();
    }
}