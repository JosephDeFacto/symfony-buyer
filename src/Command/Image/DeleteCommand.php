<?php

namespace App\Command\Image;

use App\Api\Service\ImageService;
use App\Command\CommandConstants;
use App\Repository\ImageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends Command
{
    public ImageService $imageService;
    public ImageRepository $imageRepository;

    public function __construct(ImageService $imageService, ImageRepository $imageRepository)
    {
        parent::__construct();
        $this->imageService = $imageService;
        $this->imageRepository = $imageRepository;
    }

    public function configure(): void
    {
        $this->setName('api:image:delete')
            ->setDescription('Remove images related to products')
            ->setHelp('Run this command to delete images related to products.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $data = $this->imageRepository->findAll();

        if (!$data) {
            $output->writeln('<error>' . CommandConstants::NON_EXISTS . '<error>');
            return Command::FAILURE;
        }
        $output->writeln('Deleting from database...');

        try {
            $this->imageService->delete();
            $output->writeln('<info>Images successfully deleted!</info>');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Failed to delete: ' . $exception->getMessage());
            $output->writeln('<question>Bye<question>');

            return Command::FAILURE;
        }
    }
}