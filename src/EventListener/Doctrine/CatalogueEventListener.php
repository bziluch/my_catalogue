<?php

namespace App\EventListener\Doctrine;

use App\Entity\Catalogue;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'onPrePersist', entity: Catalogue::class)]
class CatalogueEventListener
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function onPrePersist(
        Catalogue $catalogue
    ): void {
        $catalogue->setUser($this->security->getUser());
    }

}