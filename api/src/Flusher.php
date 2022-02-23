<?php

namespace App;

use Doctrine\ORM\EntityManagerInterface;

final class Flusher
{
    public function __construct(private EntityManagerInterface $em) {}

    public function flush(): void
    {
        $this->em->flush();
    }
}
