<?php

namespace App\Command;

use App\Controller\RexToBddController;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\VeloVitaController;

class VeloVitaAddUpdateProductCommand extends Command
{

    private $veloVitaController;
    private $logger;
    private $mailer;
    private $rexToBddController;

    public function __construct(VeloVitaController $veloVitaController, LoggerInterface $velovitaLogger, \Swift_Mailer $mailer, RexToBddController $rexToBddController)
    {
        $this->veloVitaController = $veloVitaController;
        $this->rexToBddController = $rexToBddController;
        $this->logger = $velovitaLogger;
        $this->mailer = $mailer;

        parent::__construct();
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:velovita:add-update';

    protected function configure()
    {
        $this
            ->setDescription('Velo Vita add or update products')
            ->setHelp('Getting nightly export from VeloVita and sending to REX POS for adding products')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $response = $this->veloVitaController->addUpdateProduct($this->rexToBddController);
            $status = $response['Status'];
            if ($status === "Failed") {
                $this->logger->error(json_encode($response));
                $message = (new \Swift_Message('Error in daily import | VeloVita | Add or update'))
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