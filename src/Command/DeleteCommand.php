<?php

namespace App\Command;

use App\Service\ProductService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends Command
{
    public ProductService $service;

    public function __construct(ProductService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function configure(): void
    {
        $this->setName('api:product:delete')
            ->setDescription('Remove data from database')
            ->setHelp('Run this command to delete data from database.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Deleting from database...');

        try {
            $this->service->delete();
            $output->writeln('<info>Successfully deleted!</info>');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Failed to delete: ' . $exception->getMessage());
            return Command::FAILURE;
        }
    }
}