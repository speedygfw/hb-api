<?php

namespace App\Command;

use App\Repository\ContractRepository;
use App\Service\CollectiveOrderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTime;

#[AsCommand(
    name: 'hb:cron',
    description: 'Runs daily cron jobs on budgetbook.',
)]
class HbCronCommand extends Command
{
    private $mr = null;

    private $cr = null;

    public function __construct(ManagerRegistry $mr, ContractRepository $cr)
    {
        $this->mr = $mr;
        $this->cr = $cr;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');
        $dt = new DateTime("NOW");

        if ($arg1) {
            // $io->note(sprintf('You passed an argument: %s', $arg1));
            $dt = new DateTime($arg1);
        }

        if ($input->getOption('option1')) {
            // ...
        }
        $cos = new CollectiveOrderService();

        $io->success("Booking created: " . $cos->do($this->mr, $this->cr, $dt));

        return Command::SUCCESS;
    }
}
