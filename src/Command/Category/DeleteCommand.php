<?php

namespace App\Command\Category;

use App\Api\Service\CategoryService;
use App\Command\CommandConstants;
use App\Repository\CategoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends Command
{
    public CategoryService $categoryService;
    public CategoryRepository $categoryRepository;
    public function __construct(CategoryService $categoryService, CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->categoryService = $categoryService;
        $this->categoryRepository = $categoryRepository;
    }

    public function configure(): void
    {
        $this->setName('api:category:delete')
            ->setDescription('Remove category from the database')
            ->setHelp('Run this command to remove data from category table.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->categoryRepository->findAll();

        if ($data) {
            $output->writeln('<error>' . CommandConstants::NON_EXISTS . '<error>');
            return Command::FAILURE;
        }

        $output->writeln('Saving to database...');

        try {
            $this->categoryService->delete();
            $output->writeln('<info>Successfully deleted!<error>');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Failed to save: ' . $exception->getMessage());
            $output->writeln('<question>Bye<question>');
            return Command::FAILURE;
        }
    }
}