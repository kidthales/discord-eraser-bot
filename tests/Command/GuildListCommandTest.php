<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Tests\CommandTestable;
use App\Tests\Entity\GuildTest;
use App\Tests\Entity\UserTest;
use App\Tests\EntityManageable;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GuildListCommandTest extends KernelTestCase
{
    use CommandTestable, EntityManageable;

    /**
     * @return CommandTester
     */
    static public function getSubject(): CommandTester
    {
        return self::getCommandTester('app:guild:list');
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_execute(): void
    {
        $guild = GuildTest::getSubject(175928847299117063, true);
        $this->entityManager->persist($guild);
        $this->entityManager->flush();

        $commandTester = self::getSubject();
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        self::assertStringContainsString('175928847299117063', $commandTester->getDisplay());
    }
}
