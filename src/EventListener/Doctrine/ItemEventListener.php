<?php

namespace App\EventListener\Doctrine;

use App\Entity\Item;
use App\Event\ItemUpdateCatalogueEvent;
use App\Service\CatalogueService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEntityListener(event: Events::preUpdate, method: 'onPreUpdate', entity: Item::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'onPostUpdate', entity: Item::class)]
#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Item::class)]
#[AsEventListener(event: ItemUpdateCatalogueEvent::class, method: 'onItemUpdateCatalogue')]
class ItemEventListener
{
    private bool $recalculateValue = false;

    public function __construct(
        private readonly CatalogueService $catalogueService,
    ) {
    }

    public function onPreUpdate(Item $item, PreUpdateEventArgs $eventArgs): void
    {
        $item->setUpdateDate(new \DateTime());

        $changesArray = $eventArgs->getEntityChangeSet();
        if (array_key_exists('pricingMin', $changesArray) || array_key_exists('pricingMax', $changesArray))
        {
            $this->recalculateValue = true;
        }
    }

    public function onPostUpdate(Item $item): void
    {
        if ($this->recalculateValue) {
            $this->catalogueService->updateCataloguePricing($item->getCatalogue());
        }
    }

    public function onPostPersist(Item $item): void
    {
        $this->catalogueService->updateCataloguePricing($item->getCatalogue());
        $this->catalogueService->updateItemsCount($item->getCatalogue());
    }

    public function onItemUpdateCatalogue(ItemUpdateCatalogueEvent $event): void
    {

        $this->catalogueService->updateCataloguePricing($event->getOldCatalogue(), false);
        $this->catalogueService->updateItemsCount($event->getOldCatalogue(), false);

        $newCatalogue = $event->getItem()->getCatalogue();
        $this->catalogueService->updateCataloguePricing($newCatalogue, false);
        $this->catalogueService->updateItemsCount($newCatalogue);
    }
}