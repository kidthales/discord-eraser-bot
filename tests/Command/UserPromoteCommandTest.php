<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Console\Command;
use App\Console\UserCommand;
use App\Entity\User;
use App\Tests\CommandTestable;
use App\Tests\Entity\UserTest;
use App\Tests\EntityManageable;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UserPromoteCommandTest extends KernelTestCase
{
    use CommandTestable, EntityManageable;

    /**
     * @return CommandTester
     */
    static public function getSubject(): CommandTester
    {
        return self::getCommandTester('app:user:promote');
    }

    /**
     * @return void
     */
    public function test_executeValidationFailure(): void
    {
        $commandTester = self::getSubject();
        $commandTester->execute([UserCommand::ARGUMENT_NAME_DISCORD_ID => 'failit']);
        self::assertEquals(Command::FAILURE, $commandTester->getStatusCode());

        $display = $commandTester->getDisplay();
        self::assertStringContainsString('This value is not a Discord Snowflake.', $display);
        self::assertStringContainsString('de1e3db3-5ed4-4941-aae4-59f3667cc3a3', $display);
    }

    /**
     * @return void
     */
    public function test_executeUserNotFoundFailure(): void
    {
        $commandTester = self::getSubject();
        $commandTester->execute([UserCommand::ARGUMENT_NAME_DISCORD_ID => 175928847299117063]);
        self::assertEquals(Command::FAILURE, $commandTester->getStatusCode());

        $display = $commandTester->getDisplay();
        self::assertStringContainsString('User not found', $display);
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_executeSuccess(): void
    {
        $user = UserTest::getSubject(175928847299117063);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $commandTester = self::getSubject();
        $commandTester->execute([UserCommand::ARGUMENT_NAME_DISCORD_ID => '175928847299117063']);
        $commandTester->assertCommandIsSuccessful();

        $display = $commandTester->getDisplay();
        self::assertStringContainsString('175928847299117063', $display);
        self::assertStringContainsString(User::ROLE_SUPER_ADMIN, $display);
    }
}
