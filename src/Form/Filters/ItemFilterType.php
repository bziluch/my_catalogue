<?php

namespace App\Form\Filters;

use App\Model\Enum\FilterTypeEnum;
use App\Model\Enum\ItemStatusEnum;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class ItemFilterType extends AbstractFilterType
{
    public static function defineFilterTypes(): array
    {
        return [
            'status' => FilterTypeEnum::Exact
        ];
    }

    protected function buildFilterForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('status', EnumType::class, [
                'label' => 'Status',
                'class' => ItemStatusEnum::class
            ]);
    }
}