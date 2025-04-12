<?php

declare(strict_types=1);

namespace App\Command;

use App\Console\Command;
use App\Entity\Guild;
use App\Repository\GuildRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(name: 'app:guild:list', description: 'List guilds', aliases: ['app:list-guilds'])]
final class GuildListCommand extends Command
{
    /**
     * @var GuildRepository
     */
    private GuildRepository $guildRepository;

    /**
     * @param GuildRepository $guildRepository
     * @return void
     */
    #[Required]
    public function setUserRepository(GuildRepository $guildRepository): void
    {
        $this->guildRepository = $guildRepository;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Guild: List');

        $this->io->table(['ID', 'Discord ID', 'Installed', 'Created', 'Updated'], array_map(fn (Guild $guild) => [
            $guild->getId(),
            $guild->getDiscordId(),
            $guild->getInstalled(),
            $guild->getCreatedAt()->format('Y-m-d H:i:s'),
            $guild->getUpdatedAt()->format('Y-m-d H:i:s')
        ], $this->guildRepository->findBy([], ['id' => 'ASC'])));

        return Command::SUCCESS;
    }
}
