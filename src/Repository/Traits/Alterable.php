<?php

declare(strict_types=1);

namespace App\Repository\Traits;

trait Alterable
{
    /**
     * @param $entity
     * @param bool $flush
     * @return void
     */
    public function add($entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $entity
     * @param bool $flush
     * @return void
     */
    public function remove($entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
