<?php

declare(strict_types=1);

namespace App\Security\Discord\Authenticator;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait UserFindableOrCreatable
{
    /**
     * @var UserRepository
     */
    private readonly UserRepository $userRepository;

    /**
     * @var ValidatorInterface
     */
    private readonly ValidatorInterface $validator;

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
     * @param string $discordId
     * @return User
     * @throws ValidatorException
     */
    private function findOrCreateUser(string $discordId): User
    {
        return $this->findUser($discordId) ?? $this->createUser($discordId);
    }

    /**
     * @param string $discordId
     * @return User|null
     */
    private function findUser(string $discordId): ?User
    {
        return $this->userRepository->findOneByDiscordId($discordId);
    }

    /**
     * @param string $discordId
     * @return User
     * @throws ValidatorException
     */
    private function createUser(string $discordId): User
    {
        $user = new User();
        $user->setDiscordId($discordId);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new ValidatorException((string)$errors);
        }

        $this->userRepository->add($user, true);

        return $user;
    }
}
