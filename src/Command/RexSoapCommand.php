<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\RexSoapController;

class RexSoapCommand extends Command
{

    private $rexSoapController;

    public function __construct(RexSoapController $rexSoapController)
    {
        $this->rexSoapController = $rexSoapController;

        parent::__construct();
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:rex-soap';

    protected function configure()
    {
        $this
            ->setDescription('All Rex Soap command')
            ->setHelp('Getting and sending to REX POS')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $test = $this->rexSoapController->testCommand();

        $output->writeln($test);
    }
}