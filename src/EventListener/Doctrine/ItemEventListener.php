<?php

namespace App\EventListener\Doctrine;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'onPreUpdate', entity: Item::class)]
class ItemEventListener
{
    public function onPreUpdate(Item $item): void
    {
        $item->setUpdateDate(new \DateTime());
    }

}