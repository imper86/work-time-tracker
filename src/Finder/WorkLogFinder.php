<?php

namespace App\Finder;

use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

class WorkLogFinder
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findDescriptionsForTask(int $taskId): array
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('wl.description')
            ->from('work_log', 'wl')
            ->andWhere('wl.task_id = :taskId')
            ->setParameter('taskId', $taskId, Types::INTEGER)
            ->orderBy('wl.started_at', 'desc')
            ->fetchAllAssociative();
    }

    public function reportDaily(DateTimeInterface $date): array
    {
        $dateFrom = sprintf('%s 00:00:00', $date->format('Y-m-d'));
        $dateTo = sprintf('%s 23:59:59', $date->format('Y-m-d'));

        $qb = $this->connection->createQueryBuilder();

        return $qb->select('t.code', 'wl.description', 'SUM(wl.duration) sum_duration')
            ->from('work_log', 'wl')
            ->leftJoin('wl', 'task', 't', 't.id = wl.task_id')
            ->andWhere('wl.started_at BETWEEN :dateFrom AND :dateTo')
            ->groupBy('wl.task_id', 'wl.description')
            ->setParameters(['dateFrom' => $dateFrom, 'dateTo' => $dateTo])
            ->fetchAllAssociative();
    }

    public function report(DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('t.code', 'wl.description', 'SUM(wl.duration) sum_duration')
            ->from('work_log', 'wl')
            ->leftJoin('wl', 'task', 't', 't.id = wl.task_id')
            ->andWhere('wl.started_at BETWEEN :dateFrom AND :dateTo')
            ->groupBy('wl.task_id', 'wl.description')
            ->setParameter('dateFrom', $from->format('Y-m-d H:i:s'), Types::STRING)
            ->setParameter('dateTo', $to->format('Y-m-d H:i:s'), Types::STRING)
            ->fetchAllAssociative();
    }

    public function totalDuration(DateTimeInterface $from, DateTimeInterface $to): int
    {
        return (int) $this->connection
            ->createQueryBuilder()
            ->select('SUM(wl.duration) sum_duration')
            ->from('work_log', 'wl')
            ->andWhere('wl.started_at BETWEEN :dateFrom AND :dateTo')
            ->setParameter('dateFrom', $from->format('Y-m-d H:i:s'), Types::STRING)
            ->setParameter('dateTo', $to->format('Y-m-d H:i:s'), Types::STRING)
            ->fetchOne() ?? 0;
    }
}