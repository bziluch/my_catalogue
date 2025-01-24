<?php

namespace App\EventListener\Doctrine;

use App\Entity\Item;
use App\Event\ItemSubmitImageEvent;
use App\Event\ItemUpdateCatalogueEvent;
use App\Service\CatalogueService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsEntityListener(event: Events::preUpdate, method: 'onPreUpdate', entity: Item::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'onPostUpdate', entity: Item::class)]
#[AsEntityListener(event: Events::prePersist, method: 'onPrePersist', entity: Item::class)]
#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Item::class)]
#[AsEventListener(event: ItemUpdateCatalogueEvent::class, method: 'onItemUpdateCatalogue')]
#[AsEventListener(event: ItemSubmitImageEvent::class, method: 'onItemSubmitImage')]
class ItemEventListener
{
    private bool $recalculateValue = false;

    public function __construct(
        private readonly CatalogueService $catalogueService,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function onPrePersist(Item $item): void
    {
        if (null !== ($file = $item->getUploadedFile())) {
            $this->eventDispatcher->dispatch(new ItemSubmitImageEvent($item, $file));
        }
    }

    public function onPostPersist(Item $item): void
    {
        $this->catalogueService->updateCataloguePricing($item->getCatalogue());
        $this->catalogueService->updateItemsCount($item->getCatalogue());
    }

    public function onPreUpdate(Item $item): void
    {
        if (null !== ($file = $item->getUploadedFile())) {
            $this->eventDispatcher->dispatch(new ItemSubmitImageEvent($item, $file));
        }
    }

    public function onPostUpdate(Item $item): void
    {
        if ($this->recalculateValue) {
            $this->catalogueService->updateCataloguePricing($item->getCatalogue());
        }
    }

    public function onItemUpdateCatalogue(ItemUpdateCatalogueEvent $event): void
    {

        $this->catalogueService->updateCataloguePricing($event->getOldCatalogue(), false);
        $this->catalogueService->updateItemsCount($event->getOldCatalogue(), false);

        $newCatalogue = $event->getItem()->getCatalogue();
        $this->catalogueService->updateCataloguePricing($newCatalogue, false);
        $this->catalogueService->updateItemsCount($newCatalogue);
    }

    public function onItemSubmitImage(ItemSubmitImageEvent $event): void
    {
        //TODO: upload image here
    }
}