<?php

namespace App\Event;

use App\Entity\Catalogue;
use App\Entity\Item;
use Symfony\Contracts\EventDispatcher\Event;

class ItemUpdateCatalogueEvent extends Event
{
    public function __construct(
        private readonly Item $item,
        private readonly Catalogue $oldCatalogue,
    ) {
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getOldCatalogue(): Catalogue
    {
        return $this->oldCatalogue;
    }

}