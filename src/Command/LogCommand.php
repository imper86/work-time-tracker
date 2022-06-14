<?php

namespace App\Command;

use App\Entity\Task;
use App\Entity\WorkLog;
use App\Finder\TaskFinder;
use App\Finder\WorkLogFinder;
use App\Repository\TaskRepository;
use App\Service\DurationHumanizer;
use Closure;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_column;
use function array_combine;
use function array_merge;
use function sprintf;

class LogCommand extends Command implements SignalableCommandInterface
{
    protected static $defaultName = 'log:run';
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;
    private bool $receivedStopSignal = false;
    private TaskFinder $taskFinder;
    private WorkLogFinder $workLogFinder;
    private DurationHumanizer $durationHumanizer;

    public function __construct(
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager,
        TaskFinder $taskFinder,
        WorkLogFinder $workLogFinder,
        DurationHumanizer $durationHumanizer
    ) {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
        $this->taskFinder = $taskFinder;
        $this->workLogFinder = $workLogFinder;
        $this->durationHumanizer = $durationHumanizer;
    }

    public function getSubscribedSignals(): array
    {
        return [SIGINT];
    }

    public function handleSignal(int $signal): void
    {
        if (SIGINT === $signal) {
            $this->receivedStopSignal = true;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $cursor = new Cursor($output);

        $task = $this->getTask($io);
        $description = $this->getLogDescription($io, $task);
        $workLog = new WorkLog(null, $task, $description, new DateTimeImmutable());

        while (false === $this->receivedStopSignal) {
            $cursor->clearLine();
            $cursor->moveToColumn(0);

            $duration = time() - $workLog->getStartedAt()->getTimestamp();
            $io->write(sprintf('%s elapsed...', $this->durationHumanizer->humanize($duration)));

            usleep(250000);
        }

        $workLog->setFinishedAt(new DateTimeImmutable());
        $task->setLastLoggedAt(new DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->persist($workLog);
        $this->entityManager->flush();

        $io->success('Thanks!');

        return 0;
    }

    private function getTask(SymfonyStyle $io): Task
    {
        $lastLoggedTasks = $this->taskFinder->findLastLogged(25);
        $code = $io->choice(
            'Choose task code (required)',
            array_merge(
                ['new' => 'Create new task'],
                array_combine(
                    array_column($lastLoggedTasks, 'code'),
                    array_column($lastLoggedTasks, 'name')
                ),
            ),
        );

        if ('new' === $code) {
            $code = $io->ask('Enter task code (required)', null, $this->getAskValidator());
        }

        $task = $this->taskRepository->findOneBy(['code' => $code]);

        if (!$task) {
            $name = $io->ask('Enter task name (required)', null, $this->getAskValidator());
            $description = $io->ask('Enter description');

            $task = new Task(null, $code, $name, $description);
        }

        return $task;
    }

    private function getAskValidator(): Closure
    {
        return function (?string $argument): string {
            if (empty($argument)) {
                throw new InvalidArgumentException('Value must not be empty');
            }

            return $argument;
        };
    }

    private function getLogDescription(SymfonyStyle $io, Task $task): string
    {
        $descriptions = $task->getId()
            ? array_column($this->workLogFinder->findDescriptionsForTask($task->getId()), 'description')
            : [];
        $question = new Question('What are you doing now?', $descriptions[0] ?? null);
        $question->setAutocompleterValues($descriptions);

        return $io->askQuestion($question);
    }
}