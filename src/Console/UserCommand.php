<?php

declare(strict_types=1);

namespace App\Console;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

// Declare abstract so it is omitted from command registration pass.
abstract class UserCommand extends Command
{
    public const string ARGUMENT_NAME_DISCORD_ID = 'discord-id';
    public const string ARGUMENT_DESCRIPTION_DISCORD_ID = 'Discord user ID';

    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @param UserRepository $userRepository
     * @return void
     */
    #[Required]
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ValidatorInterface $validator
     * @return void
     */
    #[Required]
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed $discordId
     * @return bool
     */
    protected function validateDiscordId(mixed $discordId): bool
    {
        $user = new User();
        $user->setDiscordId($discordId);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $this->io->error((string)$errors);
            return false;
        }

        return true;
    }
}
