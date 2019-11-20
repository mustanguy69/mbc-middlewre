<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\RexSoapController;
use Symfony\Component\Console\Input\ArrayInput;

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
        $bikeCorpAddUpdateProductsCommand = $this->getApplication()->find('app:bikecorp:add-update');
        $argumentsBikeCorpAddUpdateProductsCommand = [
            'command' => 'app:bikecorp:add-update'
        ];
        $BikeCorpAddUpdateProductsCommandInput = new ArrayInput($argumentsBikeCorpAddUpdateProductsCommand);
        try {
            $bikeCorpAddUpdateProductsCommand->run($BikeCorpAddUpdateProductsCommandInput, $output);
        } catch (\Exception $e) {
            var_dump($e);
        }

        $bikeCorpAdjustStockProductsCommand = $this->getApplication()->find('app:bikecorp:adjust-stock');
        $argumentsBikeCorpAdjustStockProductsCommand = [
            'command' => 'app:bikecorp:adjust-stock'
        ];
        $BikeCorpAdjustStockProductsCommandInput = new ArrayInput($argumentsBikeCorpAdjustStockProductsCommand);
        try {
            $bikeCorpAdjustStockProductsCommand->run($BikeCorpAdjustStockProductsCommandInput, $output);
        } catch (\Exception $e) {
            var_dump($e);
        }
    }
}