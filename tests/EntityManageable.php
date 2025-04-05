<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;

trait EntityManageable
{
    /**
     * @var EntityManager|null
     */
    protected ?EntityManager $entityManager;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->entityManager = self::bootKernel()->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
