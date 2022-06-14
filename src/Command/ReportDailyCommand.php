<?php

namespace App\Command;

use App\Finder\WorkLogFinder;
use App\Service\DurationHumanizer;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_map;
use function array_merge;

class ReportDailyCommand extends Command
{
    protected static $defaultName = 'report:daily';
    private WorkLogFinder $finder;
    private DurationHumanizer $humanizer;

    public function __construct(WorkLogFinder $finder, DurationHumanizer $humanizer)
    {
        parent::__construct();
        $this->finder = $finder;
        $this->humanizer = $humanizer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $from = (new DateTime())->setTime(0, 0);
        $to = (new DateTime())->setTime(23, 59, 59);

        $rows = array_map(
            fn (array $row): array => [
                $row['code'],
                $row['description'],
                $this->humanizer->humanize($row['sum_duration']),
            ],
            $this->finder->report($from, $to),
        );

        $total = $this->finder->totalDuration($from, $to);

        $io->table(
            ['Task', 'Description', 'Duration'],
            array_merge($rows, [['Total', '', $total > 0 ? $this->humanizer->humanize($total) : '-']]),
        );

        return 0;
    }
}