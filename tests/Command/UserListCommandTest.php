<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Tests\CommandTestable;
use App\Tests\Entity\UserTest;
use App\Tests\EntityManageable;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UserListCommandTest extends KernelTestCase
{
    use CommandTestable, EntityManageable;

    /**
     * @return CommandTester
     */
    static public function getSubject(): CommandTester
    {
        return self::getCommandTester('app:user:list');
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_execute(): void
    {
        $user = UserTest::getSubject(175928847299117063);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $commandTester = self::getSubject();
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        self::assertStringContainsString('175928847299117063', $commandTester->getDisplay());
    }
}
