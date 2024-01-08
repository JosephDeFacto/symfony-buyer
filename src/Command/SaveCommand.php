<?php

namespace App\Command;

use App\Service\ProductService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveCommand extends Command
{
    public ProductService $service;

    public function __construct(ProductService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function configure(): void
    {
        $this->setName('api:product:save')
            ->setDescription('Save data to database')
            ->setHelp('Run this command to populate database table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln('Saving to database...');

        try {
            $this->service->save();
            $output->writeln('<info>Successfully saved!</info>');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Failed to save: ' . $exception->getMessage());
            return Command::FAILURE;
        }
    }
}