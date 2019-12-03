<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\RexToShopifyController;

class CronDetectShopifyCommand extends Command
{

    private $rexToShopifyController;

    public function __construct(RexToShopifyController $rexToShopifyController)
    {
        $this->rexToShopifyController = $rexToShopifyController;

        parent::__construct();
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:detect-shopify';

    protected function configure()
    {
        $this->setDescription('Cron command to know if the product have been imported in shopify');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->rexToShopifyController->detectIfShopifySynced();
    }
}