<?php
// src/Command/SendTestEmailCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendTestEmailCommand extends Command
{
    protected static $defaultName = 'app:send-test-email';

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('test@example.com')
            ->to('souf07091991@gmail.com')
            ->subject('Test Symfony Mailer')
            ->text('Test de mail envoyé par la commande.');

        $this->mailer->send($email);

        $output->writeln('Email envoyé !');
        return Command::SUCCESS;
    }
}
