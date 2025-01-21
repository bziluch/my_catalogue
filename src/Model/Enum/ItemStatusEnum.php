<?php

namespace App\Model\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ItemStatusEnum: int implements TranslatableInterface
{
    case Default = 0;
    case Offer = 1;
    case Sold = 2;
    case Archived = 3;

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Domyślny',
            self::Offer => 'Oferta',
            self::Sold => 'Sprzedany',
            self::Archived => 'Zarchiwizowany',
        };
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return self::label();
    }
}
