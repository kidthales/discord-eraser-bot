<?php

declare(strict_types=1);

namespace App\Command;

use App\Console\Command;
use App\Console\UserCommand;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;

#[AsCommand(
    name: 'app:user:promote',
    description: 'Add ' . User::ROLE_SUPER_ADMIN . ' to user roles',
    aliases: ['app:promote-user']
)]
final class UserPromoteCommand extends UserCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(
            self::ARGUMENT_NAME_DISCORD_ID,
            InputArgument::REQUIRED,
            self::ARGUMENT_DESCRIPTION_DISCORD_ID
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws SerializerExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('User: Promote');

        $discordId = $input->getArgument(self::ARGUMENT_NAME_DISCORD_ID);

        if (!$this->validateDiscordId($discordId)) {
            return Command::FAILURE;
        }

        $user = $this->userRepository->findOneByDiscordId($discordId);

        if ($user === null) {
            $this->io->error('User not found');
            return Command::FAILURE;
        }

        $user->setRoles([User::ROLE_SUPER_ADMIN]);
        $this->userRepository->add($user, true);

        $this->io->definitionList(...$this->definitionListConverter->convert($user));

        return Command::SUCCESS;
    }
}
