<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Style\DefinitionListConverter;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

// Declare abstract so it is omitted from command registration pass.
abstract class Command extends BaseCommand
{
    public const int SUCCESS = BaseCommand::SUCCESS;
    public const int FAILURE = BaseCommand::FAILURE;
    public const int INVALID = BaseCommand::INVALID;

    /**
     * @var SymfonyStyle
     */
    protected SymfonyStyle $io;

    /**
     * @var DefinitionListConverter
     */
    protected DefinitionListConverter $definitionListConverter;

    /**
     * @param DefinitionListConverter $definitionListConverter
     * @return void
     */
    #[Required]
    public function setDefinitionListConverter(DefinitionListConverter $definitionListConverter): void
    {
        $this->definitionListConverter = $definitionListConverter;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }
}
