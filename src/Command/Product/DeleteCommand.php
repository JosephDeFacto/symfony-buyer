<?php

namespace App\Command\Product;

use App\Api\Service\ProductService;
use App\Command\CommandConstants;
use App\Repository\ProductRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends Command
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
        $this->setName('api:product:delete')
            ->setDescription('Remove product data from database')
            ->setHelp('Run this command to delete product data from database.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $data = $this->productRepository->findAll();

        if (!$data) {
            $output->writeln('<error>' . CommandConstants::NON_EXISTS . '<error>');
            return Command::FAILURE;
        }
        $output->writeln('Deleting from database...');

        try {
            $this->productService->delete();
            $output->writeln('<info>Successfully deleted!</info>');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Failed to delete: ' . $exception->getMessage());
            $output->writeln('<question>Bye<question>');

            return Command::FAILURE;
        }
    }
}