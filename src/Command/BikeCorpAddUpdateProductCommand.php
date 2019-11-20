<?php

namespace App\Command;

use App\Controller\RexToBddController;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\BikeCorpController;
use Symfony\Component\HttpFoundation\Request;


class BikeCorpAddUpdateProductCommand extends Command
{

    private $bikeCorpController;
    private $logger;
    private $mailer;
    private $rexToBddController;

    public function __construct(BikeCorpController $bikeCorpController, LoggerInterface $bikecorpLogger, \Swift_Mailer $mailer, RexToBddController $rexToBddController)
    {
        $this->bikeCorpController = $bikeCorpController;
        $this->rexToBddController = $rexToBddController;
        $this->logger = $bikecorpLogger;
        $this->mailer = $mailer;

        parent::__construct();
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:bikecorp:add-update';

    protected function configure()
    {
        $this
            ->setDescription('Bike corp add or update products')
            ->setHelp('Getting nightly export from BikeCorp and sending to REX POS for adding products')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $response = $this->bikeCorpController->addUpdateProduct($this->rexToBddController);
            $status = $response['Status'];
            if ($status === "Failed") {
                $this->logger->error(json_encode($response));
                $message = (new \Swift_Message('Error in daily import | BikeCorp | Add or update'))
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