<?php

namespace App\Service;

use App\Entity\Catalogue;
use Doctrine\ORM\EntityManagerInterface;

class PricingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function updateCataloguePricing(Catalogue $catalogue): void
    {
        $catalogue->recountValue();
        $this->entityManager->flush();
    }

}