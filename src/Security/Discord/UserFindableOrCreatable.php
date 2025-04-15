<?php

declare(strict_types=1);

namespace App\Security\Discord;

use App\Entity\User;
use Symfony\Component\Validator\Exception\ValidatorException;

trait UserFindableOrCreatable
{
    /**
     * @param string $discordId
     * @return User
     * @throws ValidatorException
     */
    private function findOrCreateUser(string $discordId): User
    {
        $user = $this->userRepository->findOneByDiscordId($discordId);

        if ($user === null) {
            $user = new User();
            $user->setDiscordId($discordId);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                throw new ValidatorException((string)$errors);
            }

            $this->userRepository->add($user, true);
        }

        return $user;
    }
}
