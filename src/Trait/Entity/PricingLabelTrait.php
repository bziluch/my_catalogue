<?php

namespace App\Trait\Entity;

trait PricingLabelTrait
{
    public function hasPricing(): bool
    {
        return null !== $this->getPricingMin() || null !== $this->getPricingMax();
    }

    public function hasPricingRange(): bool
    {
        return null !== $this->getPricingMin() && null !== $this->getPricingMax();
    }

    public function getPricingLabel(): string
    {
        if ($this->hasPricingRange()) {
            return $this->getPricingMin() . ' - ' . $this->getPricingMax(). ' zł';
        } elseif ($this->getPricingMin()) {
            return 'od ' . $this->getPricingMin(). ' zł';
        } elseif ($this->getPricingMax()) {
            return 'do ' . $this->getPricingMax(). ' zł';
        } else {
            return 'brak wyceny';
        }
    }
}