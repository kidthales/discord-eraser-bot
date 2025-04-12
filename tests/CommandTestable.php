<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

trait CommandTestable
{
    /**
     * @param string $commandName
     * @return CommandTester
     */
    protected static function getCommandTester(string $commandName): CommandTester
    {
        return new CommandTester(new Application(self::$kernel)->find($commandName));
    }
}
