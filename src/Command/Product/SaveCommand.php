<?php

namespace App\Command\Product;

use App\Api\Service\ProductService;
use App\Command\CommandConstants;
use App\Repository\ProductRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveCommand extends Command
{
    public ProductService $productService;
    public ProductRepository $productRepository;

    public function __construct(ProductService $productService, ProductRepository $productRepository)
    {
        parent::__construct();
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }

    public function configure(): void
    {
        $this->setName('api:product:save')
            ->setDescription('Save product data to the database')
            ->setHelp('Run this command to populate product database table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->productRepository->findAll();

        if ($data) {
            $output->writeln('<error>'. CommandConstants::EXISTS. '<error>');
            return Command::FAILURE;
        }

        $output->writeln('Saving to database...');

        try {
            $this->productService->save();
            $output->writeln('<info>Successfully saved!</info>');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Failed to save: ' . $exception->getMessage());
            $output->writeln('<question>Bye<question>');

            return Command::FAILURE;
        }
    }
}