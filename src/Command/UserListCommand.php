<?php

declare(strict_types=1);

namespace App\Command;

use App\Console\Command;
use App\Console\UserCommand;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:list', description: 'List users', aliases: ['app:list-users'])]
final class UserListCommand extends UserCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('User: List');

        $this->io->table(['ID', 'Discord ID', 'Roles', 'Created', 'Updated'], array_map(fn (User $user) => [
            $user->getId(),
            $user->getDiscordId(),
            implode(' ', $user->getRoles()),
            $user->getCreatedAt()->format('Y-m-d H:i:s'),
            $user->getUpdatedAt()->format('Y-m-d H:i:s')
        ], $this->userRepository->findBy([], ['id' => 'ASC'])));

        return Command::SUCCESS;
    }
}
