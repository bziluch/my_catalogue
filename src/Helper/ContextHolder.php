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
    private array $container = [];

    public function add(
        string $key,
        mixed $value
    ): void {

        $this->container[$key] = $value;
    }

    public function get(
        string $key
    ): mixed {

        return $this->container[$key];
    }

}