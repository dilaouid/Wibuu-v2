<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Swift_Mailer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendEmailCommand extends Command
{

    protected static $defaultName = 'send:email';
    private $mailer;
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setDescription('Command for send self email')
        ;
    }

    /**
     * @Route("/email")
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Command Send Self Email',
            '============'
        ]);
        
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('test42@gmail.com')
            ->setTo('diyaeddine.laouid@gmail.com')
            ->setBody("Test Email", 'text/html')
        ;
        $this->mailer->send($message);
        $output->writeln('Successful you send a self email');
    }
}