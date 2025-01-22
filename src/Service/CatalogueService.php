<?php

namespace App\Service;

use App\Entity\Catalogue;
use Doctrine\ORM\EntityManagerInterface;

class CatalogueService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function updateCataloguePricing(Catalogue $catalogue, bool $flush = true): void
    {
        $catalogue->recountValue();
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function updateItemsCount(Catalogue $catalogue, bool $flush = true): void
    {
        $catalogue->setItemCount($catalogue->getItems()->count());
        if ($flush) {
            $this->entityManager->flush();
        }
    }

}