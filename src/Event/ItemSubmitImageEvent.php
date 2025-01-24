<?php

namespace App\Event;

use App\Entity\Item;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\Event;

class ItemSubmitImageEvent extends Event
{
    public function __construct(
        private readonly Item $item,
        private readonly UploadedFile $uploadedFile
    ) {
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

}