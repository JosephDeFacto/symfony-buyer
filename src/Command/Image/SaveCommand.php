<?php

namespace App\Command\Image;

use App\Api\Service\ImageService;
use App\Command\CommandConstants;
use App\Repository\ImageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveCommand extends Command
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
        $this->setName('api:image:save')
            ->setDescription('Save product images to the database')
            ->setHelp('Run this command to populate images database table related to products.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->imageRepository->findAll();

        if ($data) {
            $output->writeln('<error>'. CommandConstants::EXISTS. '<error>');
            return Command::FAILURE;
        }

        $output->writeln('Saving to database...');

        try {
            $this->imageService->save();
            $output->writeln('<info>Images successfully saved!</info>');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Failed to save: ' . $exception->getMessage());
            $output->writeln('<question>Bye<question>');

            return Command::FAILURE;
        }
    }
}