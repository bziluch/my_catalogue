<?php

namespace App\EventListener\Doctrine;

use App\Entity\Catalogue;
use App\Entity\Item;
use App\Service\PricingService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'onPreUpdate', entity: Item::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'onPostUpdate', entity: Item::class)]
class ItemEventListener
{
    private bool $recount = false;

    public function __construct(
        private readonly PricingService $pricingService,
    ) {
    }

    public function onPreUpdate(Item $item, PreUpdateEventArgs $eventArgs): void
    {
        $item->setUpdateDate(new \DateTime());

        $changesArray = $eventArgs->getEntityChangeSet();
        if (array_key_exists('pricingMin', $changesArray) || array_key_exists('pricingMax', $changesArray))
        {
            $this->recount = true;
        }
    }

    public function onPostUpdate(Item $item): void
    {
        if ($this->recount) {
            $this->pricingService->updateCataloguePricing($item->getCatalogue());
        }
    }
}