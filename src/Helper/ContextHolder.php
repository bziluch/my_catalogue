<?php

namespace App\Helper;

use App\Entity\AbstractEntity;
use Doctrine\Common\Collections\Collection;

/*
 * This class manages transporting entities and collections through controller action steps.
 * Used to reduce need of overwriting index/form/view methods of AbstractAppController
 */
class ContextHolder
{
    /**
     * @var array<AbstractEntity|Collection>
     */
    private array $container = [];

    public function add(
        string $key,
        AbstractEntity|Collection $value
    ): void {

        $container[$key] = $value;
    }

    public function get(
        string $key
    ): AbstractEntity|Collection {

        return $this->container[$key];
    }

}