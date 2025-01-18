<?php

namespace App\Model\Enum;

enum ItemStatusEnum: int
{
    case Default = 0;
    case Offer = 1;
    case Sold = 2;
    case Archived = 3;
}
