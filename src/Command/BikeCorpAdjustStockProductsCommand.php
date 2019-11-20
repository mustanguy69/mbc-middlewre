<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\BikeCorpController;

class BikeCorpAdjustStockProductsCommand extends Command
{

    private $bikeCorpController;
    private $logger;
    private $mailer;

    public function __construct(BikeCorpController $bikeCorpController, LoggerInterface $bikecorpLogger, \Swift_Mailer $mailer)
    {
        $this->bikeCorpController = $bikeCorpController;
        $this->logger = $bikecorpLogger;
        $this->mailer = $mailer;

        parent::__construct();
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:bikecorp:adjust-stock';

    protected function configure()
    {
        $this
            ->setDescription('Bike corp adjust stocks')
            ->setHelp('Getting nightly export from BikeCorp and adjust stock in REX POS')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $response = $this->bikeCorpController->adjustStocksProduct();
            $status = $response['Status'];
            if ($status === "Failed") {
                $this->logger->error(json_encode($response));
                $message = (new \Swift_Message('Error in daily import | BikeCorp | Adjust stocks'))
                    ->setFrom('it@bikes.com.au')
                    ->setTo('it@bikes.com.au')
                    ->setBody(json_encode($response));

                $this->mailer->send($message);
            } else if($status === "Success") {
                $this->logger->info(json_encode($response));
            }

        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }
}