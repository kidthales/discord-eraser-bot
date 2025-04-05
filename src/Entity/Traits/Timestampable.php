<?php

/*
 * This file was part of the Doctrine Behavioral Extensions package.
 * (c) Gediminas Morkevicius <gediminas.morkevicius@gmail.com> http://www.gediminasm.org
 * The MIT license, reference http://www.opensource.org/licenses/mit-license.php
 */

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Timestampable
{
    /**
     * @var DateTimeImmutable|null
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $createdAt = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $updatedAt = null;


    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return void
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     * @return void
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
