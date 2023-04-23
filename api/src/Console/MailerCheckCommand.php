<?php

declare(strict_types=1);

namespace App\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerCheckCommand extends Command
{
    public function __construct(private ContainerInterface $container)
    {
        parent::__construct();       
    }

    protected function configure(): void
    {
        $this
            ->setName('mailer:check');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Sending</comment>');

        $message = (new Email())
            ->from('mail@app.test')
            ->to('user@app.test')
            ->html('Confirm');

        /** @var MailerInterface */
        $mailer = $this->container->get(MailerInterface::class);

        $mailer->send($message);

        $output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}
